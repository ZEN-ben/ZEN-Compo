<?php

namespace ZENben\FoosballBundle\Entity\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $google_id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $profile_picture;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var boolean
     */
    protected $signed_up;

    /**
     * @var boolean
     */
    protected $invalidated;

    public function __construct()
    {
        $this->invalidated = false;
    }

    public function isInvalidated()
    {
        return $this->invalidated;
    }

    public function setInvalidated($invalid)
    {
        $this->invalidated = $invalid;
        return $this;
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
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set profile_picture
     *
     * @param string $profilePicture
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profile_picture = $profilePicture;

        return $this;
    }

    /**
     * Get profile_picture
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profile_picture;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /* Non generated*/
    public function eraseCredentials()
    {
        return true;
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @var \DateTime
     */
    private $signed_up_date;


    /**
     * Set signed_up_date
     *
     * @param \DateTime $signedUpDate
     * @return User
     */
    public function setSignedUpDate($signedUpDate)
    {
        $this->signed_up_date = $signedUpDate;

        return $this;
    }

    /**
     * Get signed_up_date
     *
     * @return \DateTime
     */
    public function getSignedUpDate()
    {
        return $this->signed_up_date;
    }
}