<?php

namespace ZENben\GitBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use ZENben\GitBundle\Entity\Webhook;

class QueueCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('zengit:webhook:queue')
            ->setDescription('Starts the queue for webhooks processing')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $om = $this->getContainer()->get('doctrine.orm.entity_manager');
        $webhookRepo = $om->getRepository('ZENbenGitBundle:Webhook');
        while(true) {
            $inProgressWebhook = $webhookRepo->findOneBy(['status' => Webhook::STATUS_IN_PROGRESS]);
            if ($inProgressWebhook === null) {
                $newWebhook = $webhookRepo->findOneBy(['status' => Webhook::STATUS_NEW], ['id' => 'ASC']);
                if ($newWebhook === null) {
                    sleep(1);
                } else {
                    $newWebhook->setStatus(Webhook::STATUS_IN_PROGRESS);
                    $om->flush();

                    $cmd = sprintf('php app/console zengit:webhook:github %s', $newWebhook->getId());
                    $kernelRoot = $this->getContainer()->getParameter('kernel.root_dir');
                    $workingDir = realpath(sprintf('%s/../', $kernelRoot));

                    $process = new Process($cmd, $workingDir);
                    $process->run();
                }
            } else {
                sleep(1);
            }
        }
    }
}