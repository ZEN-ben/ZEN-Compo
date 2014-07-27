<?php
namespace ZENben\FoosballBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeGameUpdatesCommand extends ContainerAwareCommand
{
    
    protected $reportLog = '';
    
    protected function configure()
    {
        $this
            ->setName('foosball:upgrade_updates')
            ->setDescription('Upgrades the gameupdates to the new format')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        $gameUpdates = $em->getRepository('FoosballBundle:Game\GameUpdate')->findAll();
        $progress = $this->getHelper('progress');
        $progress->start($output, count($gameUpdates));
        foreach ($gameUpdates as $gameUpdate) {
            switch ($gameUpdate->getType()) {
                case 'match.updated':
                    $oldParameters = $gameUpdate->getParameters();
                    
                    if (! isset($oldParameters['%player_1_name%'])) {
                        continue;
                    }
                    
                    $p1Name = $this->getFirstName($oldParameters['%player_1_name%']);
                    $p2Name = $this->getFirstName($oldParameters['%player_2_name%']);
                    $user1 = $em->getRepository('FoosballBundle:User\User')->findOneByUsername($p1Name);
                    $user2 = $em->getRepository('FoosballBundle:User\User')->findOneByUsername($p2Name);
                    
                    if ($user1 === null) {
                        $this->report(sprintf('User "%s (%s)" could not be found, skipping GameUpdate#%s', $p1Name, $oldParameters['%player_1_name%'], $gameUpdate->getId()));
                        continue;
                    }
                    if ($user2 === null) {
                        $this->report(sprintf('User "%s" (%s) could not be found, skipping GameUpdate#%s', $p2Name, $oldParameters['%player_2_name%'], $gameUpdate->getId()));
                        continue;
                    }
                    
                    $parameters = [
                       '%player_1_score%' => $oldParameters['%player_1_score%'],
                       '%player_2_score%' => $oldParameters['%player_2_score%'],
                       'player_1_id' => $user1->getGoogleId(),
                       'player_2_id' => $user2->getGoogleId()
                    ];
                    $gameUpdate->setParameters($parameters);
                    break;
                case 'round.played':
                    break;
                case 'new.player':
                    $oldParameters = $gameUpdate->getParameters();
                    
                    if (! isset($oldParameters['%player%'])) {
                        continue;
                    }
                    $p1Name = $this->getFirstName($oldParameters['%player%']);
                    $user1 = $em->getRepository('FoosballBundle:User\User')->findOneByUsername($p1Name);
                    
                    if ($user1 === null) {
                        $this->report(sprintf('User "%s (%s)" could not be found, skipping GameUpdate#%s', $p1Name, $oldParameters['%player%'], $gameUpdate->getId()));
                        continue;
                    }
                    
                    $parameters = [
                       'player_id' => $user1->getGoogleId()
                    ];
                    $gameUpdate->setParameters($parameters);
                    break;
            }
            
            $progress->advance();
        }
        $em->flush();
        $progress->finish();
        
        $output->write($this->reportLog, true);
    }
    
    protected function getFirstName($fullName)
    {
        $firstName = '';
        $parts = explode(' ', $fullName);
        $useParts = [];
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if (in_array(strtolower($part), ['van', 'de', 'den'])) {
                    break;
                }
                $useParts[] = $part;
            }
            if (count($useParts) > 1) {
                array_pop($useParts);
            }
        } else {
            $useParts = [$parts[0]];
        }
        $firstName = implode(' ', $useParts);
        
        return $firstName;
    }
    
    protected function report($message)
    {
        $this->reportLog = $this->reportLog . PHP_EOL;
        $this->reportLog = $this->reportLog . $message;
    }
    
    public function getReport()
    {
        return $this->reportLog;
    }
}
