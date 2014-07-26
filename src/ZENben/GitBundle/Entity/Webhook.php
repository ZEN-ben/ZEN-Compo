<?php

namespace ZENben\GitBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ZENben\GitBundle\Service\GitHub\Commit;

/**
 * Webhook
 */
class Webhook
{

    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_DONE = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $dateFinished;

    /**
     * @var Collection
     */
    private $buildResults;

    /** @var  \stdClass */
    private $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->buildResults = new ArrayCollection();
    }

    public function getHeadCommit()
    {
        $data = $this->getData();
        return new Commit(
            $data->repository->owner->login,
            $data->repository->name,
            $data->pull_request->head->sha
        );
    }

    public function getPullRequestNumber()
    {
        return $this->getData()->number;
    }

    public function getData()
    {
        if ($this->data === null) {
            $this->data = json_decode($this->getPayload());
        }
        return $this->data;
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Webhook
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
     * Set payload
     *
     * @param string $payload
     * @return Webhook
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    
        return $this;
    }

    /**
     * Get payload
     *
     * @return string 
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Webhook
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateFinished
     *
     * @param \DateTime $dateFinished
     * @return Webhook
     */
    public function setDateFinished($dateFinished)
    {
        $this->dateFinished = $dateFinished;
    
        return $this;
    }

    /**
     * Get dateFinished
     *
     * @return \DateTime 
     */
    public function getDateFinished()
    {
        return $this->dateFinished;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Webhook
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
    private $event;


    /**
     * Set event
     *
     * @param string $event
     * @return Webhook
     */
    public function setEvent($event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return string 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add buildResults
     *
     * @param BuildResult $buildResults
     * @return Webhook
     */
    public function addBuildResult(BuildResult $buildResults)
    {
        $this->buildResults[] = $buildResults;
    
        return $this;
    }

    /**
     * Remove buildResults
     *
     * @param BuildResult $buildResults
     */
    public function removeBuildResult(BuildResult $buildResults)
    {
        $this->buildResults->removeElement($buildResults);
    }

    /**
     * Get buildResults
     *
     * @return Collection
     */
    public function getBuildResults()
    {
        return $this->buildResults;
    }
}