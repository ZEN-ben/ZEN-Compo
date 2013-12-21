<?php

namespace ZENben\FoosballBundle\Game\Type;

use ZENben\FoosballBundle\Entity\Game\GameUpdate;
use ZENben\FoosballBundle\Entity\Game\Tournament;
use ZENben\FoosballBundle\Game\GameInterface;
use ZENben\FoosballBundle\Event\GameUpdateEvent;

abstract class BaseType implements GameInterface
{
    
    const EVENT_GAME_UPDATED = 'foosball.game.updated';
    
    protected $entity;
    protected $em;
    
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;
    
    public function __construct($em, Tournament $entity, $dispatcher)
    {
        $this->em = $em;
        $this->entity = $entity;
        $this->dispatcher = $dispatcher;
    }

    public function getName()
    {
        return $this->entity->getName();
    }

    public function getDescription()
    {
        return $this->entity->getDescription();
    }

    public function getDateStart()
    {
        return $this->entity->getDateStart();
    }

    public function getDateEnded()
    {
        return $this->entity->getDateEnded();
    }

    public function addUpdate($title, $description, $type = 'default', $parameters = null)
    {
        $gameUpdate = new GameUpdate($this->entity->getGame(), $title, $description, $type, $parameters);
        $this->em->persist($gameUpdate);
        $this->em->flush();
        $this->dispatcher->dispatch(self::EVENT_GAME_UPDATED, new GameUpdateEvent($gameUpdate));
    }

    public function getUpdates()
    {
        return $this->entity->getGame()->getUpdates();
    }

    public function getYammerGroup()
    {
        return $this->entity->getGame()->getYammerGroup();
    }

}