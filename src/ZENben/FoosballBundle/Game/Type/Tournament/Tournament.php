<?php

namespace ZENben\FoosballBundle\Game\Type\Tournament;

use ZENben\FoosballBundle\Entity\User\User;
use ZENben\FoosballBundle\Game\GameState;
use ZENben\FoosballBundle\Game\Type\BaseType;

class Tournament extends BaseType {

    public function processScores($matchId, $scores) {
        $repo = $this->em->getRepository('FoosballBundle:Game\Match');
        $match = $repo->find($matchId);
        $match->setScoreRed($scores[0]);
        $match->setScoreBlue($scores[1]);

        $winner = $scores[0] > $scores[1] ? $match->getRedPlayer() : $match->getBluePlayer(); 
        
        $matches = $repo->findAll();
        $matchesCount = count($matches);
        $bracketSize = ($matchesCount + 1) / 2; 
        
        $matchId = $match->getMatchId();
        
        if ($matchId != count($matches)) {
            $nextMatchId = ceil($matchId / 2) + $bracketSize;
            $nextMatch = $repo->findOneBy(['match_id' => $nextMatchId]);
            $even = $matchId % 2 !== 0;
            if ($even) {
                $nextMatch->setRedPlayer($winner);
            } else {
                $nextMatch->setBluePlayer($winner);
            }
        }
        
        $this->progress();
        $this->em->flush();
    }

    public function progress() {
        $rounds = $this->getMatches();
        $roundsCount = count($rounds);
        
        foreach ($rounds as $round) {
            $matchesCount = count($round);
            $matchesPlayed = 0;
            $byes = 0;
            $losers = [];
            foreach($round as $match) {
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
                foreach($round as $match) {
                    if ($match->getBye() && $match->getScoreRed() === null) {
                        if ( ! $match->getRedPlayer() && count($losers) > 0) {
                            $loser = $this->em->getReference('FoosballBundle:User\User',key($losers));
                            $match->setRedPlayer($loser);
                            array_shift($losers);
                            
                            // update previous matches
                            $previousMatchId = $match->getMatchId() - count($round) - 1;
                            $previousMatch = $this->em->getRepository('FoosballBundle:Game\Match')->findOneBy(['match_id' => $previousMatchId]);
                            if ($previousMatch && $previousMatch->getBye()) {
                                $previousMatch->setRedPlayer($loser);
                            }
                        } 
                        if ( ! $match->getBluePlayer() && count($losers) > 0) {
                            $loser = $this->em->getReference('FoosballBundle:User\User',key($losers));
                            $match->setBluePlayer($loser);
                            array_shift($losers);
                            $match->setBye(false);
                            
                            // update previous matches
                            $previousMatchId = ($match->getMatchId() - count($round));
                            $previousMatch = $this->em->getRepository('FoosballBundle:Game\Match')->findOneBy(['match_id' => $previousMatchId]);
                            if ($previousMatch && $previousMatch->getBye()) {
                                $previousMatch->setRedPlayer($loser);
                            }
                        }
                        if (count($losers) < 1 && $match->getBluePlayer() === null) {
                            $match->setScoreRed(1);
                            $match->setScoreBlue(0);
                            if ($match->getRedPlayer()) {
                                // progress this player
                                $nextMatchId = ceil($match->getMatchId() / 2) + $matchesCount;
                                $nextMatch = $this->em->getRepository('FoosballBundle:Game\Match')->findOneBy(['match_id' => $nextMatchId]);
                                $nextMatch->setRedPlayer($match->getRedPlayer());
                                $nextMatch->setBye(true);
                            }
                        }
                    }
                }
            }
        }
        $this->em->flush();
    }

    public function getState() {
        if (!$this->isStarted()) {
            return GameState::WAITING_TO_START;
        } elseif ($this->getGamesLeft() > 0) {
            return GameState::IN_PROGRRESS;
        } else {
            return GameState::DONE;
        }
    }

    public function signUp($mixed, $comment = null) {
        if (is_object($mixed) && get_class($mixed) === 'ZENben\FoosballBundle\Entity\Game\TournamentSignup') {
            $this->entity->addSignup($mixed);
        } else {
            $signup = new \ZENben\FoosballBundle\Entity\Game\TournamentSignup();
            $signup->setTournament($this->entity);
            $signup->setUser($mixed);
            $signup->setComment($comment);
            $signup->setDate(new \DateTime());
            $this->em->persist($signup);
        }
        $this->em->flush();
    }

    public function getSignups() {
        return $this->entity->getSignups();
    }

    public function addParticipant(User $user) {
        $this->addSignup($user);
    }

    public function isParticipating(User $user) {
        $partcipating = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findOneBy(['user' => $user]);
        return $partcipating !== null;
    }

    public function removeParticipant(User $user) {
        $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findByUser($user)->remove();
        $this->em->flush();
    }

    public function getParticipants() {
        $signups = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findAll(['date' => 'DESC']);
        $users = [];
        foreach ($signups as $signup) {
            $users[] = $signup->getUser();
        }
        return $users;
    }

    public function getTimeUntilStart() {
        $dateStart = $this->entity->getDateStart();
        $now = new \DateTime();
        return $now->diff($dateStart);
    }

    public function isStarted() {
        return false;
    }

    public function getGamesLeft() {
        $totalGames = 10;
        $gamesPlayed = 10;
        return $totalGames - $gamesPlayed;
    }

    public function getRank(User $user) {
        if (!$this->isParticipating($user)) {
            return null;
        }
        return 23;
    }

    public function getMatches($round = null) {
        $matches = [];
        $entities = $this->em->getRepository('FoosballBundle:Game\Match')->findAll([], ['matchId' => 'ASC']);

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
}
