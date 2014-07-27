<?php

namespace ZENben\FoosballBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ZENben\Bundle\YammerBundle\Service\YammerService;
use ZENben\FoosballBundle\Game\MatchesGenerator\Tournament\EliminationGenerator;

class GameController extends Controller
{
    const REASON_NOT_CURRENT_ROUND = 'not_current_round';
    const REASON_NO_WINNER = 'no_winner';

    public function indexAction($id)
    {
        $game = $this->get('game')->getGame($id);

        if ($this->getRequest()->get('gen')) {
            $this->generateNewTournament($game);
            return $this->redirect($this->generateUrl('foosball_game', ['id' => $id]));
        }

        if ($this->getRequest()->get('del')) {
            $this->deleteTournament($id);
            return $this->redirect($this->generateUrl('foosball_game', ['id' => $id]));
        }

        if ($this->getRequest()->get('reset')) {
            $this->deleteTournament($id);
            $this->generateNewTournament($game);
            return $this->redirect($this->generateUrl('foosball_game', ['id' => $id]));
        }

        return $this->render('FoosballBundle:Game:index.html.twig', [
            'game' => $game,
            'id' => $id
        ]);
    }

    public function matchSaveAction($gameId, $matchId)
    {
        $scoreRed = intval($this->getRequest()->get('red'));
        $scoreBlue = intval($this->getRequest()->get('blue'));

        if (($scoreRed !== 10 && $scoreBlue !== 10) || $scoreBlue === $scoreRed) {
            return new JsonResponse([
                'success' => false,
                'message' => self::REASON_NO_WINNER
            ]);
        }

        $match = $this->getDoctrine()->getManager()->getRepository('FoosballBundle:Game\Match')->find($matchId);

        $game = $this->get('game')->getGame($gameId);
        $currentRound = $game->getCurrentRound();
        $gameRound = $game->getRoundForMatch($match);
        if ($currentRound !== $gameRound) {
            return new JsonResponse([
                'success' => false,
                'message' => self::REASON_NOT_CURRENT_ROUND
            ]);
        }

        $game->processScores(
            $matchId,
            [$scoreRed, $scoreBlue]
        );

        $won = 'ok';
        $user = $this->getUser();
        if ($user->getId() === $match->getBluePlayer()->getId()) {
            $won = $scoreBlue > $scoreRed ? 'won' : 'lost';
        } elseif ($user->getId() === $match->getRedPlayer()->getId()) {
            $won = $scoreRed > $scoreBlue ? 'won' : 'lost';
        }

        return new JsonResponse([
            'success' => true,
            'scoreRed' => $scoreRed,
            'scoreBlue' => $scoreBlue,
            'message' => $won
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

    /**
     * @param $game
     * @return EliminationGenerator
     */
    private function generateNewTournament($game)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $generator = new EliminationGenerator($this->getDoctrine()->getManager());
        $generator->generate($game->getParticipants(), $game);
        $this->get('game')->addUpdate($game, 'tournament.started.title', 'tournament.started.description', 'game.started');
    }

    /**
     * @return array
     */
    private function deleteTournament($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $all = $em->getRepository('ZENben\FoosballBundle\Entity\Game\Match')->findByGame($id);
        foreach ($all as $match) {
            $em->remove($match);
        }
        $allUpdates = $em->getRepository('ZENben\FoosballBundle\Entity\Game\GameUpdate')->findByGame($id);
        foreach ($allUpdates as $update) {
            $em->remove($update);
        }
        $em->flush();
    }
}
