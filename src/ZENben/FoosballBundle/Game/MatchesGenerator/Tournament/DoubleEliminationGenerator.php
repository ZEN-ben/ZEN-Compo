<?php

namespace ZENben\FoosballBundle\Game\MatchesGenerator\Tournament;

use ZENben\FoosballBundle\Entity\Game\Match;

class DoubleEliminationGenerator implements MatchesGeneratorInterface {

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function generate($participants) {
        
    }

}