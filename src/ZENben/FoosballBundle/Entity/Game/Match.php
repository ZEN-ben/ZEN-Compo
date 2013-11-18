<?php

namespace ZENben\FoosballBundle\Entity\Game;

use Doctrine\ORM\Mapping as ORM;

/**
 * Match
 */
class Match
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $datePlayed;

    /**
     * @var integer
     */
    private $scoreRed;

    /**
     * @var integer
     */
    private $scoreBlue;

    /**
     * @var \ZENben\FoosballBundle\Entity\User\User
     */
    private $red_player;

    /**
     * @var \ZENben\FoosballBundle\Entity\User\User
     */
    private $blue_player;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datePlayed
     *
     * @param \DateTime $datePlayed
     * @return Match
     */
    public function setDatePlayed($datePlayed)
    {
        $this->datePlayed = $datePlayed;

        return $this;
    }

    /**
     * Get datePlayed
     *
     * @return \DateTime
     */
    public function getDatePlayed()
    {
        return $this->datePlayed;
    }

    /**
     * Set scoreRed
     *
     * @param integer $scoreRed
     * @return Match
     */
    public function setScoreRed($scoreRed)
    {
        $this->scoreRed = $scoreRed;

        return $this;
    }

    /**
     * Get scoreRed
     *
     * @return integer
     */
    public function getScoreRed()
    {
        return $this->scoreRed;
    }

    /**
     * Set scoreBlue
     *
     * @param integer $scoreBlue
     * @return Match
     */
    public function setScoreBlue($scoreBlue)
    {
        $this->scoreBlue = $scoreBlue;

        return $this;
    }

    /**
     * Get scoreBlue
     *
     * @return integer
     */
    public function getScoreBlue()
    {
        return $this->scoreBlue;
    }

    /**
     * Set red_player
     *
     * @param \ZENben\FoosballBundle\Entity\User\User $redPlayer
     * @return Match
     */
    public function setRedPlayer(\ZENben\FoosballBundle\Entity\User\User $redPlayer = null)
    {
        $this->red_player = $redPlayer;

        return $this;
    }

    /**
     * Get red_player
     *
     * @return \ZENben\FoosballBundle\Entity\User\User
     */
    public function getRedPlayer()
    {
        return $this->red_player;
    }

    /**
     * Set blue_player
     *
     * @param \ZENben\FoosballBundle\Entity\User\User $bluePlayer
     * @return Match
     */
    public function setBluePlayer(\ZENben\FoosballBundle\Entity\User\User $bluePlayer = null)
    {
        $this->blue_player = $bluePlayer;

        return $this;
    }

    /**
     * Get blue_player
     *
     * @return \ZENben\FoosballBundle\Entity\User\User
     */
    public function getBluePlayer()
    {
        return $this->blue_player;
    }

    /**
     * @var string
     */
    private $match_id;


    /**
     * Set match_id
     *
     * @param string $matchId
     * @return Match
     */
    public function setMatchId($matchId)
    {
        $this->match_id = $matchId;

        return $this;
    }

    /**
     * Get match_id
     *
     * @return string
     */
    public function getMatchId()
    {
        return $this->match_id;
    }

    /**
     * @var boolean
     */
    private $bye = false;


    /**
     * Set bye
     *
     * @param boolean $bye
     * @return Match
     */
    public function setBye($bye)
    {
        $this->bye = $bye;

        return $this;
    }

    /**
     * Get bye
     *
     * @return boolean
     */
    public function getBye()
    {
        return $this->bye;
    }

    public function isPlayed()
    {
        return $this->scoreRed !== null;
    }
}