<?php

namespace ZENben\GitBundle\Service\GitHub;

use Github\Client as GitHubClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class GitHubService
{

    const STATUS_PENDING = 'pending';
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    /**
     *
     * @var  GitHubClient
     */
    protected $gitHubClient;

    /**
     *
     * @var RouterInterface
     */
    protected $router;

    public function __construct($token, RouterInterface $router)
    {
        $gitHubClient = new GitHubClient();
        $gitHubClient->authenticate($token, null, GithubClient::AUTH_HTTP_TOKEN);
        $this->gitHubClient = $gitHubClient;
        $this->router = $router;
    }

    public function getDiffs(Commit $base)
    {
        $url = sprintf(
            '%srepos/%s/%s/compare/master...%s:%s',
            $this->gitHubClient->getOption('base_url'),
            $base->getUser(),
            $base->getRepo(),
            $base->getUser(),
            $base->getSha()
        );
        $diffResp = $this->gitHubClient->getHttpClient()->get($url);

        return json_decode($diffResp->getBody())->files;
    }

    public function createCodeComment(CodeComment $codeComment)
    {
        $json = json_encode(
            [
                'body' => $codeComment->getMessage(),
                'commit_id' => $codeComment->getCommit()->getSha(),
                'path' => $codeComment->getFile(),
                'position' => $codeComment->getPosition()
            ]
        );

        $this->gitHubClient->getHttpClient()->post(
            sprintf(
                '/repos/%s/%s/pulls/%s/comments',
                $codeComment->getCommit()->getUser(),
                $codeComment->getCommit()->getRepo(),
                $codeComment->getPullRequest()
            ),
            $json
        );
    }

    /**
     * @param int    $webhookId
     * @param Commit $commit
     * @param string $state
     * @param string $description
     */
    public function statusChange($webhookId, Commit $commit, $state, $description)
    {
        $json = json_encode(
            [
                'state' => $state,
                'description' => $description,
                'target_url' => $this->router->generate(
                    'zenben_git_status', [
                        'id' => $webhookId
                    ], UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );

        $this->gitHubClient->getHttpClient()->post(
            sprintf(
                '%srepos/%s/%s/statuses/%s',
                $this->gitHubClient->getOption('base_url'),
                $commit->getUser(),
                $commit->getRepo(),
                $commit->getSha()
            ), $json
        );
    }
}
