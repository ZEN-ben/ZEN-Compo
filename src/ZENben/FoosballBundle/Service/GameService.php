<?php

namespace ZENben\FoosballBundle\Service;

use ZENben\FoosballBundle\Entity\Game\GameUpdate;
use ZENben\FoosballBundle\Event\GameUpdateEvent;

class GameService
{
    const EVENT_GAME_UPDATED = 'foosball.game.updated';
    
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
        $gameInstance = new $gameInstanceType($this->em, $gameEntity, $this);
        return $gameInstance;
    }
    
    public function addUpdate($game, $title, $description, $type = 'default', $parameters = [])
    {
        $gameUpdate = new GameUpdate($game->getGame(), $title, $description, $type, $parameters);
        $this->em->persist($gameUpdate);
        $this->em->flush();
        $this->dispatcher->dispatch(self::EVENT_GAME_UPDATED, new GameUpdateEvent($gameUpdate));
    }
}
