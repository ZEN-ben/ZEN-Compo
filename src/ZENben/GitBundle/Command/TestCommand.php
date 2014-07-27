<?php

namespace ZENben\GitBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('testing command')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {


    }
}
