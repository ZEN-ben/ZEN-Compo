<?php

namespace ZENben\FoosballBundle\Service;

class GameService
{
    protected $em;
    protected $config;
    protected $gameInstance;
    protected $dispatcher;

    public function __construct($em, $config, $dispatcher)
    {
        $this->em = $em;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
    }

    public function getGame($id)
    {
        $game = $this->em->getRepository('FoosballBundle:Game\Game')->find($id);
        $entityType = $this->config['type'][$game->getType()]['entity'];
        $gameEntity = $this->em->getRepository($entityType)->find($game->getGameId());
        $gameInstanceType = $this->config['type'][$game->getType()]['instance'];
        $gameInstance = new $gameInstanceType($this->em, $gameEntity, $this->dispatcher);
        return $gameInstance;
    }

}