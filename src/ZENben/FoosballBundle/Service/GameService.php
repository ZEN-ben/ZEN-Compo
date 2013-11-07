<?php

namespace ZENben\FoosballBundle\Service;

class GameService
{
    protected $em;
    protected $config;
    protected $gameInstance;

    public function __construct($em, $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    public function getGame($id)
    {
        $game = $this->em->getRepository('FoosballBundle:Game\Game')->find($id);
        $entityType = $this->config['type'][$game->getType()]['entity'];
        $gameEntity = $this->em->getRepository($entityType)->find($game->getGameId());
        $gameInstanceType = $this->config['type'][$game->getType()]['instance'];
        $gameInstance = new $gameInstanceType($this->em, $gameEntity);
        return $gameInstance;
    }

}