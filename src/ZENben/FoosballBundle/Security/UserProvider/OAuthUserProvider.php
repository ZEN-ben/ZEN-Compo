<?php

namespace ZENben\FoosballBundle\Security\UserProvider;

use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider as BaseEntityUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class OAuthuserProvider extends BaseEntityUserProvider implements UserProviderInterface
{
    protected $em;

    public function __construct(ManagerRegistry $registry, $class, array $properties, $managerName = null)
    {
        $this->em = $registry->getManager($managerName);
        parent::__construct($registry, $class, $properties, $managerName);
    }

    public function loadUserByUsername($google_id) {
        return $this->repository->findOneBy(array('google_id' => $google_id));
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $username = $response->getUsername();
        $user = $this->repository->findOneBy(array($this->properties[$resourceOwnerName] => $username));

        if (null === $user) {
            $profilePicture = $response->getProfilePicture() ? $response->getProfilePicture() : 'https://lh3.googleusercontent.com/-G0f6aoCwegQ/AAAAAAAAAAI/AAAAAAAAAAA/xPuAYZmM39E/s120-c/photo.jpg';

            $user = new \ZENben\FoosballBundle\Entity\User\User();
            $user->setEmail($response->getEmail());
            $user->setGoogleId($response->getUsername());
            $user->setUsername($response->getRealName());
            $user->setProfilePicture($profilePicture);
            $user->setRoles(['ROLE_USER', 'ROLE_OAUTH_USER']);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user) {
        if ($user->isInvalidated()) {
            $user = $this->loadUserByUsername($user->getGoogleId());
        }
        return $user;
    }

    public function supportsClass($class) {
        return true;
    }

}
