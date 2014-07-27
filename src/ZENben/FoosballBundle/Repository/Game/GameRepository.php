<?php

namespace ZENben\FoosballBundle\Repository\Game;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
    
    public function findAllPlayed()
    {
        return $this->_em->createQueryBuilder($order = 'DESC')
            ->select('game')
            ->from('FoosballBundle:Game\Game', 'game')
            ->where('game.dateEnded IS NOT NULL')
            ->orderBy('game.dateEnded', $order)
            ->getQuery()->getResult();
    }
}
