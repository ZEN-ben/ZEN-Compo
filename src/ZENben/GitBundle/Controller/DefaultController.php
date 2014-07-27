<?php

namespace ZENben\GitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Process;
use ZENben\GitBundle\Entity\Webhook;
use ZENben\GitBundle\Service\GitHub\GitHubService;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $secret = $this->container->getParameter('github_secret');
        $signature = $this->getRequest()->headers->get('X-Hub-Signature');
        $requestContent = $this->getRequest()->getContent();

        if (! $this->isGenuineGithubRequest($secret, $signature, $requestContent)) {
            return new JsonResponse(['error' => 'Incorrect secret'], 403);
        }
        $event = $this->getRequest()->headers->get('X-GitHub-Event');
        $supportedWebhooks =
            [
                'pull_request'
            ];
        if (! in_array($event, $supportedWebhooks)) {
            return new JsonResponse(['error' => sprintf("Event '%s' is not implemented.", $event)], 400);
        }
        $content = json_decode($requestContent, true);
        switch ($content['action']) {
            case 'open':
            case 'synchronize':
                $this->placeWebhookInQueue($event, $requestContent);
                break;
        }
        return new JsonResponse(
            [
                'status' => 'OK'
            ]
        );
    }

    public function statusAction($id)
    {
        $webhook = $this->getDoctrine()->getManager()->find('ZENbenGitBundle:Webhook', $id);

        return $this->render(
            'ZENbenGitBundle:Default:status.html.twig',
            [
                'webhook' => $webhook
            ]
        );
    }

    /**
     * @param string $secret
     * @param string $signature
     * @param string $requestContent
     * @return bool
     */
    public function isGenuineGithubRequest($secret, $signature, $requestContent)
    {
        list($algo, $githubHash) = explode('=', $signature, 2);
        $computedHash = hash_hmac($algo, $requestContent, $secret);
        if ($computedHash !== $githubHash) {
            return false;
        }
        return true;
    }

    /**
     * @param $event
     * @param $requestContent
     */
    protected function placeWebhookInQueue($event, $requestContent)
    {
        $webhook = new Webhook();
        $webhook->setEvent($event);
        $webhook->setDateCreated(new \DateTime());
        $webhook->setStatus(Webhook::STATUS_NEW);
        $webhook->setPayload($requestContent);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($webhook);
        $objectManager->flush();

        $this->get('zengit.github')->statusChange(
            $webhook->getId(),
            $webhook->getHeadCommit(),
            GitHubService::STATUS_PENDING,
            'Code check is in queue..'
        );
    }
}
