<?php

namespace ZENben\FoosballBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class GameUpdateEvent extends Event
{
    
    protected $gameUpdate;
    
    public function __construct($gameUpdate)
    {
        $this->gameUpdate = $gameUpdate;
    }
    
    public function getGameUpdate()
    {
        return $this->gameUpdate;
    }
    
/*    protected $type;
    protected $winner;
    protected $loser;
    
    protected $scoreBlue;
    protected $scoreRed;
    
    protected $playerBlue;
    protected $playerRed;
    
    public function getWinner()
    {
        return $this->winner;
    }
    
    public function getLoser()
    {
        return $this->loser;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getScoreBlue()
    {
        return $this->scoreBlue;
    }
    
    public function getScoreRed()
    {
        return $this->scoreRed;
    }*/
}
