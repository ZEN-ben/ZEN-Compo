<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $games = $this->getDoctrine()->getManager()
            ->getRepository('FoosballBundle:Game\Game')->findBy(['dateEnded' => null], ['dateStart' => 'ASC']);
        
        $gamesPlayed =  $this->getDoctrine()->getManager()
            ->getRepository('FoosballBundle:Game\Game')->findAllPlayed();
        
        return $this->render(
            'FoosballBundle:Default:index.html.twig',
            [
                'games' => $games,
                'gamesPlayed' => $gamesPlayed,
            ]
        );
    }

    public function feedbackAction()
    {
        $title = $this->getRequest()->request->get('title');
        $description = $this->getRequest()->request->get('description');
        $this->get('targetprocess')->addRequest($title, $description);
    }
}
