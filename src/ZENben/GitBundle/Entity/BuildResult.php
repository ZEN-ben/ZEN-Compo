<?php

namespace ZENben\GitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuildResult
 */
class BuildResult
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $log;

    /**
     * @var \ZENben\GitBundle\Entity\Webhook
     */
    private $webhook;

    /**
     * @var \DateTime
     */
    private $date;

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
     * Set status
     *
     * @param  integer $status
     * @return BuildResult
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
     * Set log
     *
     * @param  string $log
     * @return BuildResult
     */
    public function setLog($log)
    {
        $this->log = $log;
    
        return $this;
    }

    /**
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set webhook
     *
     * @param  \ZENben\GitBundle\Entity\Webhook $webhook
     * @return BuildResult
     */
    public function setWebhook(\ZENben\GitBundle\Entity\Webhook $webhook = null)
    {
        $this->webhook = $webhook;
    
        return $this;
    }

    /**
     * Get webhook
     *
     * @return \ZENben\GitBundle\Entity\Webhook
     */
    public function getWebhook()
    {
        return $this->webhook;
    }

    /**
     * Set date
     *
     * @param  \DateTime $date
     * @return BuildResult
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
