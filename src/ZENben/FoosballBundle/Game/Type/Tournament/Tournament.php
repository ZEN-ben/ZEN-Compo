<?php

namespace ZENben\FoosballBundle\Game\Type\Tournament;

use ZENben\FoosballBundle\Entity\User\User;
use ZENben\FoosballBundle\Entity\Game\Match;
use ZENben\FoosballBundle\Game\GameState;
use ZENben\FoosballBundle\Game\Type\BaseType;

class Tournament extends BaseType
{
    
    public function processMatch(Match $match) {
        throw new Exception('Not yet implemented');
    }

    public function getState() {
        if ( ! $this->isStarted()) {
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
        $partcipating = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findOneBy(['user'=>$user]);
        return $partcipating !== null;
    }
    
    public function removeParticipant(User $user) {
        $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findByUser($user)->remove();
        $this->em->flush();
    }

    public function getParticipants() {
        $signups = $this->em->getRepository('FoosballBundle:Game\TournamentSignup')->findAll(['date'=>'DESC']);
        $users = [];
        foreach($signups as $signup) {
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
        if ( ! $this->isParticipating($user)) {
            return null;
        }
        return 23;
    }
    
    public function getMatches($round = null) {
        $matches = [];
        $entities = $this->em->getRepository('FoosballBundle:Game\Match')->findAll([],['matchId'=>'ASC']);
        
        $amount = count($entities);
        $bracketSize = ($amount+1)/2;
        $i = 0;
        $currentRound = 1;
        
        foreach($entities as $match) {
            if ($i >= $bracketSize) {
                $currentRound++;
                $i = 0;
                $bracketSize = $bracketSize/2;
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
