<?php

namespace ZENben\GitBundle\Twig;

use ZENben\GitBundle\Entity\Webhook;
use ZENben\GitBundle\Service\GitHub\GitHubService;

class UtilsExtension extends \Twig_Extension
{

    public function getGlobals()
    {
        return [];
    }

    public function getFunctions()
    {
        return [];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('webhook_status', [$this, 'filterWebhookStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('github_status', [$this, 'filterGithubStatus'], ['is_safe' => ['html']])
        ];
    }

    public function filterWebhookStatus($status)
    {
        $statusString = '<span style="font-style: italic;">unknown status</span>';
        switch ($status) {
            case Webhook::STATUS_DONE:
                $statusString = 'done';
                break;
            case Webhook::STATUS_IN_PROGRESS:
                $statusString = 'in progress';
                break;
            case Webhook::STATUS_NEW:
                $statusString = 'queued';
                break;
        }

        return '<span class="status-webhook-'.$status.'">'.strtoupper($statusString).'</span>';
    }

    public function filterGithubStatus($status)
    {
        $statusString = '<span style="font-style: italic;">unknown status</span>';
        switch ($status) {
            case GitHubService::STATUS_PENDING:
                $statusString = 'pending';
                break;
            case GitHubService::STATUS_SUCCESS:
                $statusString = 'success';
                break;
            case GitHubService::STATUS_ERROR:
                $statusString = 'error';
                break;
        }

        return '<span class="status-github-'.$status.'">'.strtoupper($statusString).'</span>';
    }

    public function getName()
    {
        return 'utils';
    }
}
