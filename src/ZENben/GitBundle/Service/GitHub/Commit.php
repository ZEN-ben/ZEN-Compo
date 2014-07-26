<?php

namespace ZENben\GitBundle\Service\GitHub;

class Commit
{

    /**
    * 
    * @var string */
    protected $user;
    /**
    * 
    * @var  string */
    protected $repo;
    /**
    * 
    * @var  string */
    protected $sha;

    function __construct($user, $repo, $sha)
    {
        $this->user = $user;
        $this->repo = $repo;
        $this->sha = $sha;
    }

    /**
     * @return string
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param string $repo
     */
    public function setRepo($repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
    }

    /**
     * @param string $sha
     */
    public function setSha($sha)
    {
        $this->sha = $sha;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}
