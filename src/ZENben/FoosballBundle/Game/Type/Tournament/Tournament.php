<?php

namespace ZENben\FoosballBundle\Game\Type\Tournament;

use ZENben\FoosballBundle\Entity\User\User;
use ZENben\FoosballBundle\Game\GameState;
use ZENben\FoosballBundle\Game\Type\BaseType;
use ZENben\FoosballBundle\Entity\Game\TournamentSignup;

class Tournament extends BaseType
{

    public function processScores($matchId, $scores)
    {
        $oldRound = $this->getCurrentRound();
        $repo = $this->em->getRepository('FoosballBundle:Game\Match');
        $match = $repo->find($matchId);
        $match->setScoreRed($scores[0]);
        $match->setScoreBlue($scores[1]);

        $winner = null;
        if ($scores[0] > $scores[1]) {
            $winner = $match->getRedPlayer();
            $loser = $match->getBluePlayer();
            $winnerScore = $scores[0];
            $loserScore = $scores[1];
        } else {
            $winner = $match->getBluePlayer();
            $loser = $match->getRedPlayer();
            $winnerScore = $scores[1];
            $loserScore = $scores[0];
        }

        $matches = $repo->findBy(['game' => $match->getGame()]);
        $matchesCount = count($matches);
        $bracketSize = ($matchesCount + 1) / 2;

        if ($winner && $match->getMatchId() != count($matches)) {
            $this->promoteWinnerToNextRound($match, $bracketSize, $repo, $winner);
        }

        $this->addMatchPlayedUpdate($winner, $loser, $winnerScore, $loserScore);

        $this->progress();
        
        if ($oldRound !== $this->getCurrentRound()) {
            if ($this->getGamesLeft() > 0) {
                $this->addNewRoundUpdate($oldRound);
            } else {
                $this->finishTournament($winner);
            }
        }

        $this->em->flush();
    }

    public function finishTournament($winner)
    {
        $parameters = [
            '%winner%' => $winner->getUserName(),
            '%winner_id%' => $winner->getGoogleId(),
        ];
        $this->addUpdate('', '', 'tournament.winner', $parameters);
        $this->setDateEnded(new \DateTime());
    }
    
    public function progress()
    {
        $rounds = $this->getMatches();

        foreach ($rounds as $roundNumber => $round) {
            $this->processRound($round);
        }
        $this->em->flush();
    }

    public function getState()
    {
        if (!$this->isStarted()) {
            return GameState::WAITING_TO_START;
        } elseif ($this->getGamesLeft() > 0) {
            return GameState::IN_PROGRRESS;
        } else {
            return GameState::DONE;
        }
    }

    public function signUp($mixed, $comment = null)
    {
        if (is_object($mixed) && get_class($mixed) === 'ZENben\FoosballBundle\Entity\Game\TournamentSignup') {
            $this->entity->addSignup($mixed);
        } else {
            $signup = new TournamentSignup();
            $signup->setTournament($this->entity);
            $signup->setUser($mixed);
            $signup->setDate(new \DateTime());
            $this->em->persist($signup);
            $params = [
                'player_id' => $mixed->getId()
            ];
            $this->addUpdate('new.player', $comment, 'new.player', $params);
        }
        $this->em->flush();
    }

    public function getSignups()
    {
        return $this->entity->getSignups();
    }

    public function addParticipant(User $user)
    {
        $this->addSignup($user);
    }

    public function isParticipating(User $user)
    {
        $partcipating = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findOneBy([
            'user' => $user,
            'tournament' => $this->entity->getId()
        ]);
        return $partcipating !== null;
    }

    public function removeParticipant(User $user)
    {
        $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findByUser($user)->remove();
        $this->em->flush();
    }

    public function getParticipants()
    {
        $signups = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findBy([
            'tournament' => $this->entity->getId()
        ],[
            'date' => 'DESC',
            'id' => 'DESC'
        ]);
        $users = [];
        foreach ($signups as $signup) {
            $users[] = $signup->getUser();
        }
        return $users;
    }

    public function getTimeUntilStart()
    {
        $dateStart = $this->entity->getGame()->getDateStart();
        $now = new \DateTime();
        return $now->diff($dateStart);
    }

    public function isStarted()
    {
        return count($this->getMatches()) > 0;
    }

    public function getGamesLeft()
    {
        $rounds = $this->getMatches();
        $total = 0;
        $played = 0;
        
        foreach($rounds as $matches) {
            foreach($matches as $match) {
                $total++;
                if ($match->getScoreRed()) {
                    $played++;
                }
            }
        }
        return $total - $played;
    }

    public function getRank(User $user)
    {
        throw new \Exception('Not yet implemented');
    }

    public function getCurrentRound()
    {
        $rounds = $this->getMatches();
        foreach ($rounds as $round => $matches) {
            foreach ($matches as $match) {
                if ($match->isPlayed() === false) {
                    return $round;
                }
            }
        }
    }

    public function getRoundForMatch($match)
    {
        foreach ($this->getMatches() as $round => $matches) {
            foreach ($matches as $loopMatch) {
                if ($match->getId() === $loopMatch->getId()) {
                    return $round;
                }
            }
        }
        throw new \Exception('The match is not part of this tournament');
    }

    public function getMatches($round = null)
    {
        $matches = [];
        $entities = $this->em->getRepository('FoosballBundle:Game\Match')->findBy([
            'game' => $this->entity->getGame()->getId()
        ]);

        $amount = count($entities);
        $bracketSize = ($amount + 1) / 2;
        $i = 0;
        $currentRound = 1;

        foreach ($entities as $match) {
            if ($i >= $bracketSize) {
                $currentRound++;
                $i = 0;
                $bracketSize = $bracketSize / 2;
            }
            if ($round !== null) {
                if ($currentRound === $round) {
                    $matches[] = $match;
                }
            } else {
                $matches[$currentRound][] = $match;
            }

            $i++;
        }
        return $matches;
    }

    /**
     * @param $round
     */
    private function processRound($round)
    {
        $matchesCount = count($round);
        $matchesPlayed = 0;
        $byes = 0;
        $losers = [];
        foreach ($round as $match) {
            if ($match->getBluePlayer() !== null && $match->getScoreBlue() !== null) {
                $matchesPlayed++;
                $loser = $match->getScoreRed() < $match->getScoreBlue() ? $match->getRedPlayer()->getId() : $match->getBluePlayer()->getId();
                $losers["$loser "] = abs($match->getScoreBlue() - $match->getScoreRed());
            }
            $byes = $match->getBye() ? $byes + 1 : $byes;
        }

        if (count($losers) > $byes * 2) {
            asort($losers);
        } else {
            arsort($losers);
        }

        if ($matchesCount === $matchesPlayed + $byes) {
            $this->allGamesPlayedProcessed($round, $losers, $matchesCount);
        }
    }

    /**
     * @param $round
     * @param $losers
     * @param $matchesCount
     */
    private function allGamesPlayedProcessed($round, $losers, $matchesCount)
    {
        foreach ($round as $match) {
            if ($match->getBye() && $match->getScoreRed() === null) {
                if (!$match->getRedPlayer() && count($losers) > 0) {
                    $loser = $this->em->getReference('FoosballBundle:User\User', trim(key($losers)));
                    $match->setRedPlayer($loser);
                    array_shift($losers);

                    // update previous matches
                    $previousMatchId = $match->getMatchId() - count($round) - 1;
                    $previousMatch = $this->em->getRepository('FoosballBundle:Game\Match')
                            ->findOneBy(['match_id' => $previousMatchId, 'game' => $match->getGame()]);
                    if ($previousMatch && $previousMatch->getBye()) {
                        $previousMatch->setRedPlayer($loser);
                    }
                }
                if (!$match->getBluePlayer() && count($losers) > 0) {
                    $loser = $this->em->getReference('FoosballBundle:User\User', trim(key($losers)));
                    $match->setBluePlayer($loser);
                    array_shift($losers);
                    $match->setBye(false);

                    // update previous matches
                    $previousMatchId = ($match->getMatchId() - count($round));
                    $previousMatch = $this->em->getRepository('FoosballBundle:Game\Match')
                            ->findOneBy(['match_id' => $previousMatchId]);
                    if ($previousMatch && $previousMatch->getBye()) {
                        $previousMatch->setRedPlayer($loser);
                    }
                }

                if ($match->getBluePlayer() === null && count($losers) === 0) {
                    $match->setScoreRed(1);
                    $match->setScoreBlue(0);
                    if ($match->getRedPlayer()) {
                        // progress this player
                        $nextMatchId = ceil($match->getMatchId() / 2) + $matchesCount;
                        $nextMatch = $this->em->getRepository('FoosballBundle:Game\Match')
                                ->findOneBy(['match_id' => $nextMatchId,'game' => $match->getGame()]);
                        
                        $even = $match->getMatchId() % 2 === 0;
                        if ($even) {
                            $nextMatch->setBluePlayer($match->getRedPlayer());
                        } else {
                            $nextMatch->setRedPlayer($match->getRedPlayer());
                        }
                        
                        $nextMatch->setBye(true);
                    }
                }
            }
        }
    }

    /**
     * @param $winner
     * @param $loser
     * @param $winnerScore
     * @param $loserScore
     * @return array
     */
    private function addMatchPlayedUpdate($winner, $loser, $winnerScore, $loserScore)
    {
        $parameters = [
            '%player_1_score%' => $winnerScore,
            '%player_2_score%' => $loserScore,
            'player_1_id' => $winner->getGoogleId(),
            'player_2_id' => $loser->getGoogleId()
        ];
        $this->addUpdate('', '', 'match.updated', $parameters);
    }

    /**
     * @param $oldRound
     */
    private function addNewRoundUpdate($oldRound)
    {
        $parameters = [
            '%round_number%' => $oldRound
            //'losers' => $losers
        ];
        $this->addUpdate('round.played.title', 'round.played.description', 'round.played', $parameters);
    }

    /**
     * @param $match
     * @param $bracketSize
     * @param $repo
     * @param $winner
     */
    private function promoteWinnerToNextRound($match, $bracketSize, $repo, $winner)
    {
        $nextMatchId = ceil($match->getMatchId() / 2) + $bracketSize;
        $nextMatch = $repo->findOneBy([
            'match_id' => $nextMatchId,
            'game' => $match->getGame()
        ]);
        $even = $match->getMatchId() % 2 === 0;
        if ($even) {
            $nextMatch->setBluePlayer($winner);
        } else {
            $nextMatch->setRedPlayer($winner);
        }
    }
}
