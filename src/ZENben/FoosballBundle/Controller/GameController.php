<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use ZENben\FoosballBundle\Entity\User\UserInfo;

class GameController extends Controller
{
    public function indexAction($id) {
        $game = $this->get('game')->getGame($id);
        
        if ($this->getRequest()->get('gen')) {
            $generator = new \ZENben\FoosballBundle\Game\MatchesGenerator\Tournament\EliminationGenerator($this->getDoctrine()->getManager());
            $generator->generate($game->getParticipants());
        }
        
        return $this->render('FoosballBundle:Game:index.html.twig',[
            'game' => $game,
            'id' => $id
        ]);
    }

    public function signUpAction() {
        $comment = $this->getRequest()->get('comment');
        $game = $this->getRequest()->get('game');
        
        if (strlen($comment) > 45 || strlen($comment) < 5) {
            return new JsonResponse(['success' => false]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->find('FoosballBundle:User\User',$this->getUser()->getId());
        $this->get('game')->getGame($game)->signUp($user, $comment);

        return new JsonResponse([
            'success' => true,
            'comment' => $comment
        ]);
    }

}
