<?php

namespace ZENben\FoosballBundle\Entity\Game;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
 */
class Tournament
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateStart;

    /**
     * @var \DateTime
     */
    private $dateEnded;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $signups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $matches;

    /**
     * @var \ZENben\FoosballBundle\Entity\Game\Game
     */
    private $game;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->signups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->matches = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Tournament
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnded
     *
     * @param \DateTime $dateEnded
     * @return Tournament
     */
    public function setDateEnded($dateEnded)
    {
        $this->dateEnded = $dateEnded;

        return $this;
    }

    /**
     * Get dateEnded
     *
     * @return \DateTime
     */
    public function getDateEnded()
    {
        return $this->dateEnded;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tournament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Tournament
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add signups
     *
     * @param \ZENben\FoosballBundle\Entity\Game\TournamentSignup $signups
     * @return Tournament
     */
    public function addSignup(\ZENben\FoosballBundle\Entity\Game\TournamentSignup $signups)
    {
        $this->signups[] = $signups;

        return $this;
    }

    /**
     * Remove signups
     *
     * @param \ZENben\FoosballBundle\Entity\Game\TournamentSignup $signups
     */
    public function removeSignup(\ZENben\FoosballBundle\Entity\Game\TournamentSignup $signups)
    {
        $this->signups->removeElement($signups);
    }

    /**
     * Get signups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSignups()
    {
        return $this->signups;
    }

    /**
     * Add matches
     *
     * @param \ZENben\FoosballBundle\Entity\Game\Match $matches
     * @return Tournament
     */
    public function addMatche(\ZENben\FoosballBundle\Entity\Game\Match $matches)
    {
        $this->matches[] = $matches;

        return $this;
    }

    /**
     * Remove matches
     *
     * @param \ZENben\FoosballBundle\Entity\Game\Match $matches
     */
    public function removeMatche(\ZENben\FoosballBundle\Entity\Game\Match $matches)
    {
        $this->matches->removeElement($matches);
    }

    /**
     * Get matches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Set game
     *
     * @param \ZENben\FoosballBundle\Entity\Game\Game $game
     * @return Tournament
     */
    public function setGame(\ZENben\FoosballBundle\Entity\Game\Game $game = null)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \ZENben\FoosballBundle\Entity\Game\Game 
     */
    public function getGame()
    {
        return $this->game;
    }
}