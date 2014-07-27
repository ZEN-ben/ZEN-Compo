<?php

namespace ZENben\FoosballBundle\Entity\Game;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameUpdate
 */
class GameUpdate
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     */
    private $dateModified;

    /**
     * @var array
     */
    private $parameters;

    public function __construct($game, $title, $description, $type = 'default', $parameters)
    {
        $this->dateCreated = new \DateTime();
        $this->game = $game;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->parameters = $parameters;
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
     * Set type
     *
     * @param  string $type
     * @return GameUpdate
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
     * Set title
     *
     * @param  string $title
     * @return GameUpdate
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return GameUpdate
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
     * Set dateCreated
     *
     * @param  \DateTime $dateCreated
     * @return GameUpdate
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param  \DateTime $dateModified
     * @return GameUpdate
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
    /**
     * @var \ZENben\FoosballBundle\Entity\Game\Game
     */
    private $game;


    /**
     * Set game
     *
     * @param  \ZENben\FoosballBundle\Entity\Game\Game $game
     * @return GameUpdate
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

    /**
     * Set parameters
     *
     * @param  array $parameters
     * @return GameUpdate
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    
        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
