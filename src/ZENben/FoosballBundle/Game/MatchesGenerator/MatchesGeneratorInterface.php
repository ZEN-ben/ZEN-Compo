<?php

namespace ZENben\FoosballBundle\Game\MatchesGenerator;

interface MatchesGeneratorInterface
{
    public function __construct($em);

    public function generate($participants);
}