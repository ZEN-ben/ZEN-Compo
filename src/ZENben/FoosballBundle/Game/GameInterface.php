<?php

namespace ZENben\FoosballBundle\Game;

interface GameInterface
{
    public function getParticipants();

    public function addParticipant(\ZENben\FoosballBundle\Entity\User\User $user);

    public function removeParticipant(\ZENben\FoosballBundle\Entity\User\User $user);

    public function isParticipating(\ZENben\FoosballBundle\Entity\User\User $user);

    public function getTimeUntilStart();

    public function isStarted();

    public function processScores($matchId, $scores);

    public function getState();

    public function getGamesLeft();

    public function getRank(\ZENben\FoosballBundle\Entity\User\User $user);
}
