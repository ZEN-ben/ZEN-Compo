<?php

namespace ZENben\GitBundle\Service\GitHub;

class CodeComment
{

    /** @var Commit */
    protected $commit;

    /** @var integer */
    protected $pullRequest;

    /** @var string */
    protected $message;

    /** @var string */
    protected $file;

    /** @var integer */
    protected $position;

    /**
     * @param Commit $commit
     * @param integer $pullRequest
     * @param string $message
     * @param string $file
     * @param integer $position
     */
    public function __construct(Commit $commit, $pullRequest, $message, $file, $position)
    {
        $this->commit = $commit;
        $this->pullRequest = $pullRequest;
        $this->message = $message;
        $this->file = $file;
        $this->position = $position;
    }

    /**
     * @return Commit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param Commit $commit
     */
    public function setCommit($commit)
    {
        $this->commit = $commit;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPullRequest()
    {
        return $this->pullRequest;
    }

    /**
     * @param int $pullRequest
     */
    public function setPullRequest($pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }
}
