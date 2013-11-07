<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        $matches = $this->getDoctrine()->getManager()->getRepository('FoosballBundle:Game\Match')->findAll();
        return $this->render('FoosballBundle:Admin:index.html.twig', ['matches' => $matches]);
    }

}
