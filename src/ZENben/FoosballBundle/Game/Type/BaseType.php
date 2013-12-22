<?php

namespace ZENben\FoosballBundle\Game\Type;

use ZENben\FoosballBundle\Entity\Game\GameUpdate;
use ZENben\FoosballBundle\Entity\Game\Tournament;
use ZENben\FoosballBundle\Game\GameInterface;
use ZENben\FoosballBundle\Event\GameUpdateEvent;

use ZENben\FoosballBundle\Service\GameService;

abstract class BaseType implements GameInterface
{
    
    protected $entity;
    protected $em;
    
    /**
     * @var GameService
     */
    protected $gameService;
    
    public function __construct($em, Tournament $entity, GameService $gameService)
    {
        $this->em = $em;
        $this->entity = $entity;
        $this->gameService = $gameService;
        
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
        return $this->entity->getGame()->getDateStart();
    }

    public function getDateEnded()
    {
        return $this->entity->getGame()->getDateEnded();
    }

    public function setDateEnded($date)
    {
        $this->entity->getGame()->setDateEnded($date);
        $this->em->flush();
    }
    
    public function addUpdate($title, $description, $type = 'default', $parameters = null)
    {
        $this->gameService->addUpdate($this, $title, $description, $type, $parameters);
    }

    public function getUpdates()
    {
        return $this->entity->getGame()->getUpdates();
    }

    public function getYammerGroup()
    {
        return $this->entity->getGame()->getYammerGroup();
    }

    public function getGame()
    {
        return $this->entity->getGame();
    }
    
}