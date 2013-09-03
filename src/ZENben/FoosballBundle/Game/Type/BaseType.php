<?php

namespace ZENben\FoosballBundle\Game\Type;

use ZENben\FoosballBundle\Game\GameInterface;

abstract class BaseType implements GameInterface
{
    protected $entity;
    protected $em;

    public function __construct($em, \ZENben\FoosballBundle\Entity\Game\Tournament $entity) {
        $this->em = $em;
        $this->entity = $entity;
    }

    public function getName() {
        return $this->entity->getName();
    }
    
    public function getDescription() {
        return $this->entity->getDescription();
    }
    
    public function getDateStart() {
        return $this->entity->getDateStart();
    }

    public function getDateEnded() {
        return $this->entity->getDateEnded();
    }
    
}