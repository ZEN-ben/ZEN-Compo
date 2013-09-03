<?php

namespace ZENben\FoosballBundle\Game;

abstract class GameState
{
    const WAITING_TO_START = 'waiting';
    const IN_PROGRRESS = 'inprogress';
    const DONE = 'done';   
}