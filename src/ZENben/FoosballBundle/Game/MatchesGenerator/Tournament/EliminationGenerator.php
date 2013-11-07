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

    public function generate($participants)
    {
        $participantsArray = [];
        $amount = pow(2, ceil(log(count($participants)) / log(2)));
        for ($i = 0; $i < $amount / 2; $i++) {
            $participantsArray[] = array_shift($participants);
            $participantsArray[] = array_pop($participants);
        }

        $bracketSize = $amount / 2;
        $matchId = 1;
        $matches = [];

        // Round 1 -- assign players
        for ($i = 0; $i < $amount; $i++) {
            $match = new Match();
            $match->setMatchId($matchId++);
            $match->setRedPlayer($participantsArray[$i++]);
            $match->setBluePlayer($participantsArray[$i]);
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
                $match->setMatchId($matchId++);
                $this->em->persist($match);
                $matches[] = $match;
            }
        }

        $this->em->flush();

        return $matches;
    }
}