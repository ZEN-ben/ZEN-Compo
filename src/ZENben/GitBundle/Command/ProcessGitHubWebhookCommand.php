<?php

namespace ZENben\GitBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use ZENben\GitBundle\Entity\BuildResult;
use ZENben\GitBundle\Entity\Webhook;
use ZENben\GitBundle\Service\GitHub\CodeComment;
use ZENben\GitBundle\Service\GitHub\Commit;
use ZENben\GitBundle\Service\GitHub\GitHubService;

class ProcessGitHubWebhookCommand extends ContainerAwareCommand
{

    /**
    * @var  ObjectManager */
    protected $objectManager;
    /**
    * @var  GitHubService */
    protected $github;
    /**
    * @var  OutputInterface */
    protected $outputStream;

    /**
    * @var  string */
    protected $status;

    /**
    * @var  Webhook */
    protected $webhook;
    /**
    * @var  Commit */
    protected $commit;
    /**
    * @var  BuildResult */
    protected $buildResult;

    /**
    * @var  string[] */
    protected $log = [];

    const FLUSH_BUFFER_MAX = 1000;
    protected $lastFlush;

    /**
    * @var  AnsiToHtmlConverter */
    protected $ansiConverter;

    protected $nextColor = null;

    protected $buildsDir = null;

    protected function checkoutBranch()
    {
        $url = sprintf(
            'git@github.com:%s/%s.git',
            $this->commit->getUser(),
            $this->commit->getRepo()
        );

        $this->git('fetch', ['origin']);
        $this->git('clean', ['--force']);
        $this->git('checkout', ['origin/master', '--force']);
        $this->git('remote', ['remove', 'downstream']);
        $this->git('remote', ['add', 'downstream', '-f', $url]);
        $this->git('branch', ['-D', 'build-' . $this->commit->getSha()]);
        $this->git('checkout', ['-B', 'build-' . $this->commit->getSha(), 'origin/master']);
        $this->git('merge', [$this->commit->getSha()]);
    }

    public function configure()
    {
        $this
            ->setName('zengit:webhook:github')
            ->setDescription('Processes GitHub webhooks')
            ->addArgument(
                'webhook_id',
                InputArgument::REQUIRED,
                'The ID for the webhook in the DB'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lastFlush = microtime(true);
        $this->ansiConverter = new AnsiToHtmlConverter(null, false);
        $id = $input->getArgument('webhook_id');
        $this->objectManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->outputStream = $output;

        $this->webhook = $this->objectManager->find('ZENbenGitBundle:Webhook', $id);
        $this->buildsDir = $this->getContainer()->getParameter('builds_dir');

        $this->buildResult = new BuildResult();
        $this->buildResult->setDate(new \DateTime());
        $this->buildResult->setWebhook($this->webhook);
        $this->buildResult->setStatus(GitHubService::STATUS_PENDING);
        $this->buildResult->setLog('');
        $this->objectManager->persist($this->buildResult);
        $this->webhook->addBuildResult($this->buildResult);
        $this->objectManager->flush();

        $this->commit = $this->webhook->getHeadCommit();

        $this->github = $this->getContainer()->get('zengit.github');
        $this->outputColor('blue');
        $this->output('Code check starting...', true, GitHubService::STATUS_PENDING);
        $this->outputColor('end');

        $this->output(sprintf('Getting diff for commit %s', $this->commit->getSha()));
        $diffs = $this->github->getDiffs($this->commit);

        $this->output('Checking diff for forbidden expressions..');

        $errors = $this->checkDiffs($diffs);
        if (count($errors) > 0) {
            $this->handleErrors($errors);
        } else {
            $this->outputColor('green');
            $this->output('Code check OK! Checking out code to build..', true, GitHubService::STATUS_PENDING);
        }
        $this->outputColor('end');

        $this->outputColor('blue');
        $this->output('Fetching origin and checking out master branch + merge commit..');
        $this->outputColor('end');
        $this->cloneIfNotExists();
        $this->checkoutBranch();

        $this->outputColor('blue');
        $this->output('Running PHPUnit..', true);
        $this->outputColor('end');
        $phpunitOutput = $this->phpunit();
        $this->handlePhpUnitErrors($phpunitOutput);

        $this->outputColor('blue');
        $this->output('Running PHP_CS..', true);
        $this->outputColor('end');
        $phpCsOutput = $this->phpCs();
        if (strpos($phpCsOutput, 'ERROR')) {
            $this->outputColor('red');
            $message = 'This code is not PSR2 compliant, please check the log for details.';
            $this->output($message, true, GitHubService::STATUS_ERROR);
        }

        if ($this->status === GitHubService::STATUS_PENDING) {
            $this->outputColor('green');
            $message = 'Your code passed all checks! Click details for the log.';
            $this->output($message, true, GitHubService::STATUS_SUCCESS);
        } else {
            $this->outputColor('red');
            $this->output('Issues have been detected. Please fix them and push again to this pull request.');
        }
        $this->outputColor('end');
        $this->webhook->setStatus(Webhook::STATUS_DONE);
        $this->objectManager->flush();

        $this->buildResult->setLog($this->ansiConverter->convert(implode("\n", $this->log)));
        $this->buildResult->setStatus($this->status);
        $this->objectManager->flush();

        return 0;
    }

    protected function cloneIfNotExists()
    {
        $repoDir = $this->buildsDir . '/' . $this->commit->getRepo();
        if (is_dir($repoDir)) {
            $this->output('The repository is already cloned.');
            return;
        }
        $process = new Process(
            sprintf(
                'git clone git@github.com:%s/%s.git ' . $repoDir,
                $this->commit->getUser(),
                $this->commit->getRepo()
            ),
            $this->buildsDir
        );
        $process->setTimeout(null);
        $process->start();
        $process->wait(
            function ($type, $buffer) {
            $this->output($buffer, null, GitHubService::STATUS_PENDING, false);
            }
        );

        $composer = $this->getContainer()->getParameter('composer');
        $process = new Process(
            sprintf('php %s install --no-interaction', $composer),
            $repoDir
        );
        $process->setTimeout(null);
        $process->start();
        $process->wait(
            function ($type, $buffer) {
            $this->output($buffer, null, GitHubService::STATUS_PENDING, false);
            }
        );

        return;
    }

    /**
     * @param $phpunitOutput
     */
    public function handlePhpUnitErrors($phpunitOutput)
    {
        if (strpos($phpunitOutput, 'FAILURES') !== false) {
            $failedTests = $this->getPhpUnitFailedTests($phpunitOutput);
            foreach ($failedTests as $test) {
                $this->output(sprintf('[%s] %s', $test['test'], $test['message']));
            }

            if (count($failedTests) === 1) {
                $test = $failedTests[0];
                $testName = $test['test'];
                $message = $test['message'];
                $message = sprintf('PHPUnit test failed: [%s] %s', $testName, $message);
                $this->output($message, true, GitHubService::STATUS_ERROR);
            } else {
                $message = sprintf('Multiple PHPUnit test failed, click details to see log.');
                $this->output($message, true, GitHubService::STATUS_ERROR);
            }
        }
    }

    protected function phpCs()
    {
        $processBuilder = new ProcessBuilder();
        $repoDirectory = sprintf('%s/%s', $this->buildsDir, $this->commit->getRepo());
        $processBuilder
            ->setTimeout(false)
            ->setWorkingDirectory($repoDirectory)
            ->add('./bin/phpcs')
        //            ->add('--report=summary')
            ->add('--report-width=120')
            ->add('--encoding=utf-8')
        //            ->add('-p')
            ->add('--standard=PSR2')
            ->add('--ignore=lib,library')
            ->add($repoDirectory.'/src')
        ;

        $process = $processBuilder->getProcess();
        $process->start();
        $process->setTimeout(false);
        $process->wait(
            function ($type, $buffer) {
                $this->output($buffer);
            }
        );

        return $process->getOutput();
    }

    protected function git($command, $argments = null)
    {
        $buildsDir = sprintf('%s/%s', $this->buildsDir, $this->commit->getRepo());

        $argumentsString = implode(' ', $argments);

        $process = new Process(sprintf('git %s %s', $command, $argumentsString), $buildsDir);
        $process->start();
        $process->setTimeout(false);
        $process->wait(
            function ($type, $buffer) {
                $this->output($buffer, null, GitHubService::STATUS_PENDING, false);
            }
        );
    }

    protected function phpunit()
    {
        $container = $this->getContainer();
        $config = $container->getParameter('phpunit');
        $buildDir = $this->buildsDir . '/' . $this->commit->getRepo();

        $processBuilder = ProcessBuilder::create();
        $processBuilder
            ->setWorkingDirectory($buildDir)
            ->setTimeout(false)
            ->add('./bin/phpunit.bat')
            ->add('--log-json')->add('phpunit-json-output.json')
            ->add('-c')->add($config['config'])
        ;
        if ($config['filter']) {
            $processBuilder->add('--filter ' . $config['filter']);
        }
        $process = $processBuilder->getProcess();
        $process->start();
        $process->wait(
            function ($type, $buffer) {
                $this->output($buffer, null, GitHubService::STATUS_PENDING, false);
            }
        );

        return $process->getOutput();
    }

    /**
     * @return array
     */
    protected function getPhpUnitFailedTests()
    {
        $buildDir = $this->buildsDir . '/' . $this->commit->getRepo();
        $content = file_get_contents($buildDir . '/phpunit-json-output.json');
        $content = '[' . str_replace('}{', "},\n{", $content) . ']';
        $tests = json_decode($content, true);
        $failedTests = [];
        foreach ($tests as $test) {
            if (isset($test['status'])) {
                if ($test['status'] !== 'pass') {
                    $failedTests[] = $test;
                }
            }
        }
        return $failedTests;
    }

    /**
     * @param $diffs
     * @return CodeComment[]
     */
    protected function checkDiffs($diffs)
    {
        $errors = [];
        foreach ($diffs as $diff) {
            if (!isset($diff->patch)) {
                continue;
            }
            $patch = $diff->patch;
            $changedLine = explode("\n", $patch);
            $position = 1;
            foreach ($changedLine as $line) {
                if (strpos($line, '+') !== 0) {
                    if (strpos($line, '@@') === 0) {
                        $stripped = \strtr($line, ['@'=>'','-'=>'','+'=>'',' '=>',']);
                        $position = intval(explode(',', $stripped)[1]);
                    }
                } else {
                    $forbidden = [
                        [
                            'regex' => '/console\.log/',
                            'warning' => 'console.log detected!'
                        ],
                        [
                            'regex' => '/var_dump/',
                            'warning' => 'var_dump detected!'
                        ],
                        [
                            'regex' => '/TODO/',
                            'warning' => 'TODO detected!'
                        ],
                        [
                            'regex' => '/\@return mixed/',
                            'warning' => 'Possible incorrect auto generated PHPDoc.'
                        ],
                    ];

                    foreach ($forbidden as $rule) {
                        if (preg_match($rule['regex'], $line)) {
                            $codeComment = new CodeComment(
                                $this->commit,
                                $this->webhook->getPullRequestNumber(),
                                $rule['warning'],
                                $diff->filename,
                                $position - 1
                            );
                            $errors[] = $codeComment;
                        }
                    }
                }
                $position++;
            }
        }
        return $errors;
    }

    /**
     * @param $errors CodeComment[]
     * @return string
     */
    protected function handleErrors($errors)
    {
        $errorCount = count($errors);

        if (count($errors) === 1) {
            $remote = $errors[0]->getMessage();
        } else {
            $remote = 'Various problems have been detected, click details for more information.';
        }

        if ($errorCount < 10) {
            foreach ($errors as $error) {
                $this->github->createCodeComment($error);
            }
        } else {
            $remote .= ' Note: Due to the large amount of errors, they have not been added as code comments.';
        }
        $local = '';
        foreach ($errors as $error) {
            $local .= sprintf("[%s:%s] %s\n", $error->getFile(), $error->getPosition(), $error->getMessage());
        }
        $this->outputColor('red');
        $this->output($local, $remote, GitHubService::STATUS_ERROR);
        $this->outputColor('end');
    }

    /**
     * @param $local
     * @param null|string|true $remote  if true it will copy the value of $local
     * @param string           $status
     * @param bool             $newLine
     */
    protected function output($local, $remote = null, $status = GitHubService::STATUS_PENDING, $newLine = true)
    {
        if ($this->nextColor) {
            $local = $this->nextColor . $local;
            $this->nextColor = null;
        }

        $this->outputStream->write($local, $newLine, OutputInterface::OUTPUT_RAW);
        if ($newLine) {
            $this->log[] = $local;
        } else {
            $lastLog = end($this->log);
            array_pop($this->log);
            $this->log[] = $lastLog . $local;
        }

        if ($this->buildResult) {
            $this->buildResult->setLog($this->ansiConverter->convert(implode("\n", $this->log)));
            $this->buildResult->setStatus($this->status);

            $msPassed = (microtime(true) - $this->lastFlush) * 1000;
            if ($msPassed > self::FLUSH_BUFFER_MAX) {
                $this->objectManager->flush();
                $this->lastFlush = microtime(true);
            }
        }

        $firstError = false;
        if ($this->status !== GitHubService::STATUS_ERROR) {
            $this->status = $status;
            $firstError = true;
        }
        if (! $firstError && $this->status === GitHubService::STATUS_ERROR) {
            return;
        }
        if ($remote === true) {
            $remote = strip_tags($this->ansiConverter->convert($local));
        }
        if ($status !== $this->status && $remote === null) {
            throw new \InvalidArgumentException('To change the status, you must provide a remote message.');
        }
        if ($remote) {
            if (strlen($remote) > 140) {
                $remote = substr($remote, 0, 140);
            }
            $this->github->statusChange($this->webhook->getId(), $this->commit, $this->status, $remote);
        }
        return;
    }

    protected function outputColor($color)
    {
        switch ($color) {
            case 'green':
                $this->nextColor = "\033[37;42m";
                break;
            case 'blue':
                $this->nextColor = "\033[37;44m";
                break;
            case 'red':
                $this->nextColor = "\033[37;41m";
                break;
            case 'end':
                $this->nextColor = "\033[0m";
                break;
            default:
                throw new \Exception('Color not supported.');
        }
    }
}
