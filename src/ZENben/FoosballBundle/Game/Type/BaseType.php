<?php

namespace ZENben\FoosballBundle\Game\Type;

use ZENben\FoosballBundle\Entity\Game\GameUpdate;
use ZENben\FoosballBundle\Game\GameInterface;

abstract class BaseType implements GameInterface
{
    protected $entity;
    protected $em;

    public function __construct($em, \ZENben\FoosballBundle\Entity\Game\Tournament $entity)
    {
        $this->em = $em;
        $this->entity = $entity;
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
    }

    public function getUpdates()
    {
        return $this->entity->getGame()->getUpdates();
    }

}