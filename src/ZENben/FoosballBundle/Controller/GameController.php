<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameController extends Controller
{
    public function indexAction($id)
    {
        $game = $this->get('game')->getGame($id);

        if ($this->getRequest()->get('gen')) {
            $generator = new \ZENben\FoosballBundle\Game\MatchesGenerator\Tournament\EliminationGenerator($this->getDoctrine()->getManager());
            $generator->generate($game->getParticipants());
        }

        if ($this->getRequest()->get('del')) {
            $em = $this->getDoctrine()->getManager();
            $all = $em->getRepository('ZENben\FoosballBundle\Entity\Game\Match')->findAll();
            foreach ($all as $match) {
                $em->remove($match);
            }
            $em->flush();
        }

        return $this->render('FoosballBundle:Game:index.html.twig', [
            'game' => $game,
            'id' => $id
        ]);
    }

    public function matchSaveAction($gameId, $matchId)
    {
        $scoreRed = $this->getRequest()->get('red');
        $scoreBlue = $this->getRequest()->get('blue');

        $match = $this->getDoctrine()->getManager()->getRepository('FoosballBundle:Game\Match')->find($matchId);
        $this->get('game')->getGame($gameId)->processScores(
            $matchId,
            [$scoreRed, $scoreBlue]
        );

        $won = 0;
        $user = $this->getUser();
        if ($user->getId() === $match->getBluePlayer()->getId()) {
            $won = $scoreBlue > $scoreRed ? 1 : -1;
        } elseif ($user->getId() === $match->getRedPlayer()->getId()) {
            $won = $scoreRed > $scoreBlue ? 1 : -1;
        }

        return new JsonResponse([
            'success' => true,
            'scoreRed' => $scoreRed,
            'scoreBlue' => $scoreBlue,
            'won' => $won
        ]);
    }

    public function signUpAction()
    {
        $comment = $this->getRequest()->get('comment');
        $game = $this->getRequest()->get('game');

        if (strlen($comment) > 45 || strlen($comment) < 5) {
            return new JsonResponse(['success' => false]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->find('FoosballBundle:User\User', $this->getUser()->getId());
        $this->get('game')->getGame($game)->signUp($user, $comment);

        return new JsonResponse([
            'success' => true,
            'comment' => $comment
        ]);
    }

}
