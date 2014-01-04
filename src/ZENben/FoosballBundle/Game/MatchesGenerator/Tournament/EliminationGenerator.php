<?php

namespace ZENben\FoosballBundle\Game\MatchesGenerator\Tournament;

use ZENben\FoosballBundle\Entity\Game\Match;
use ZENben\FoosballBundle\Game\MatchesGenerator\MatchesGeneratorInterface;

class EliminationGenerator implements MatchesGeneratorInterface
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function generate($participants, $game)
    {
        $amount = pow(2, ceil(log(count($participants)) / log(2)));
        
        
        $participantsArray = [];
        for ($i = 0; $i < $amount / 2; $i++) {
            // Strongest versus weakest mode: 
            /*
            $participantsArray[] = array_shift($participants);
            $participantsArray[] = array_pop($participants);
            */
            
            // Closest match first mode:
            $participantsArray[] = array_shift($participants);
            $participantsArray[] = array_shift($participants);
        }
        
        $bracketSize = $amount / 2;
        $matchId = 1;
        $matches = [];

        $game = $game->getGame();
        
        // Round 1 -- assign players
        for ($i = 0; $i < $amount; $i++) {
            $match = new Match();
            $match->setMatchId($matchId++);
            $match->setRedPlayer($participantsArray[$i++]);
            $match->setBluePlayer($participantsArray[$i]);
            $match->setGame($game);
            if ($match->getBluePlayer() === null) {
                $match->setBye(true);
            }
            $matches[] = $match;
            $this->em->persist($match);
        }

        // Rest of rounds
        while (($bracketSize = $bracketSize / 2) > 0.5) {
            for ($i = 0; $i < $bracketSize; $i++) {
                $match = new Match();
                $match->setGame($game);
                $match->setMatchId($matchId++);
                $this->em->persist($match);
                $matches[] = $match;
            }
        }

        $this->em->flush();

        return $matches;
    }
}