<?php

namespace ZENben\FoosballBundle\Entity\Game;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 */
class Game
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var string
     */
    private $game_form;

    /**
     * @var int
     */
    private $game_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $updates;

    /**
     * @var integer
     */
    private $yammer_group;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $matches;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->updates = new ArrayCollection();
        $this->matches = new ArrayCollection();
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
     * Set active
     *
     * @param  boolean $active
     * @return Game
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set game_form
     *
     * @param  string $gameForm
     * @return Game
     */
    public function setGameForm($gameForm)
    {
        $this->game_form = $gameForm;

        return $this;
    }

    /**
     * Get game_form
     *
     * @return string
     */
    public function getGameForm()
    {
        return $this->game_form;
    }

    /**
     * Set game_id
     *
     * @param  \int $gameId
     * @return Game
     */
    public function setGameId($gameId)
    {
        $this->game_id = $gameId;

        return $this;
    }

    /**
     * Get game_id
     *
     * @return \int
     */
    public function getGameId()
    {
        return $this->game_id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Game
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
     * @var string
     */
    private $type;


    /**
     * Set type
     *
     * @param  string $type
     * @return Game
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @var string
     */
    private $description;


    /**
     * Set description
     *
     * @param  string $description
     * @return Game
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
     * Add updates
     *
     * @param  \ZENben\FoosballBundle\Entity\Game\GameUpdate $updates
     * @return Game
     */
    public function addUpdate(\ZENben\FoosballBundle\Entity\Game\GameUpdate $updates)
    {
        $this->updates[] = $updates;
    
        return $this;
    }

    /**
     * Remove updates
     *
     * @param \ZENben\FoosballBundle\Entity\Game\GameUpdate $updates
     */
    public function removeUpdate(\ZENben\FoosballBundle\Entity\Game\GameUpdate $updates)
    {
        $this->updates->removeElement($updates);
    }

    /**
     * Get updates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUpdates()
    {
        return $this->updates;
    }

    /**
     * Set yammer_group
     *
     * @param  integer $yammerGroup
     * @return Game
     */
    public function setYammerGroup($yammerGroup)
    {
        $this->yammer_group = $yammerGroup;
    
        return $this;
    }

    /**
     * Get yammer_group
     *
     * @return integer
     */
    public function getYammerGroup()
    {
        return $this->yammer_group;
    }
    
    /**
     * Add matches
     *
     * @param  \ZENben\FoosballBundle\Entity\Game\Match $matches
     * @return Game
     */
    public function addMatch(\ZENben\FoosballBundle\Entity\Game\Match $matches)
    {
        $this->matches[] = $matches;
    
        return $this;
    }

    /**
     * Remove matches
     *
     * @param \ZENben\FoosballBundle\Entity\Game\Match $matches
     */
    public function removeMatch(\ZENben\FoosballBundle\Entity\Game\Match $matches)
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
     * Add matches
     *
     * @param  \ZENben\FoosballBundle\Entity\Game\Match $matches
     * @return Game
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
     * @var \DateTime
     */
    private $dateStart;

    /**
     * @var \DateTime
     */
    private $dateEnded;


    /**
     * Set dateStart
     *
     * @param  \DateTime $dateStart
     * @return Game
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
     * @param  \DateTime $dateEnded
     * @return Game
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
}
