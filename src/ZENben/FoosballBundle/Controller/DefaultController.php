<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $games = $this->getDoctrine()->getManager()->getRepository('FoosballBundle:Game\Game')->findAll();
        return $this->render('FoosballBundle:Default:index.html.twig', ['games' => $games]);
    }

    public function feedbackAction()
    {
        $title = $this->getRequest()->request->get('title');
        $description = $this->getRequest()->request->get('description');
        $this->get('targetprocess')->addRequest($title, $description);
    }

}
