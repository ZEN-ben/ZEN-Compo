<?php

namespace ZENben\FoosballBundle\Twig;

use Twig_SimpleFunction;
use Twig_SimpleTest;

class FoosballExtension extends \Twig_Extension
{

    protected $em;
    protected $gameService;
    protected $userService;

    public function __construct($em, $gameService, $userService)
    {
        $this->em = $em;
        $this->gameService = $gameService;
        $this->userService = $userService;
    }

    public function getGlobals()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'profile_container',
                [$this, 'profileContainer'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html']
                ]
            ),
            new Twig_SimpleFunction('game', [$this, 'getGame']),
            new Twig_SimpleFunction('user', [$this, 'getUser']),
        ];
    }

    public function getGame($id)
    {
        return $this->gameService->getGame($id);
    }

    public function getUser($id)
    {
        return $this->userService->loadUserByUsername($id);
    }

    public function getTests()
    {
        return [
            new Twig_SimpleTest(
                'participating',
                [$this, 'isParticipating']
            )
        ];
    }

    public function isParticipating(\ZENben\FoosballBundle\Entity\User\User $user, $game)
    {
        return $game->isParticipating($user);
    }

    public function profileContainer(\Twig_Environment $environment, $user)
    {
        if (is_string($user)) {
            $user = $this->em->getRepository('FoosballBundle:')->findOneByUsername($user);
        }
        return $environment->render('FoosballBundle:Macro:profile_container.html.twig', ['user' => $user]);
    }

    public function getName()
    {
        return 'foosball';
    }
}
