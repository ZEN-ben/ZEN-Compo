<?php

namespace ZENben\FoosballBundle\Listener;

use ZENben\Bundle\YammerBundle\Service\YammerService;
use ZENben\FoosballBundle\Event\GameUpdateEvent;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Psr\Log\LoggerInterface;

class YammerListener
{
    /**
     * @var type YammerService
     */
    protected $service;
    
    /**
     * @var Translator
     */
    protected $translator;
    
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    public function __construct(YammerService $service, Translator $translator, $logger)
    {
        $this->service = $service;
        $this->translator = $translator;
        $this->logger = $logger;
    }
    
    public function onGameUpdate(GameUpdateEvent $event)
    {
        $gameUpdate = $event->getGameUpdate();
        $group = $gameUpdate->getGame()->getYammerGroup();
        
        
        //TODO: this will need an update before it will work: some parameters
        //      should be processed into actual string (they might be user ids now)
        /*switch ($gameUpdate->getType()) {
            case 'match.updated':
                $message = $this->translator->trans('p1.won.agianst.p2', $gameUpdate->getParameters(), 'game_updates');
                break;
            case 'new.player':
                $titleKey = $gameUpdate->getTitle();
                $title = $this->translator->trans($titleKey, $gameUpdate->getParameters(), 'game_updates');
                $description = sprintf(
                    '.. %s: "%s"',
                    $this->translator->trans('new.player.and.said', $gameUpdate->getParameters(), 'game_updates'),
                    $gameUpdate->getDescription()
                );
                $message = sprintf('%s %s', $title, $description);
                break;
            case 'tournament.winner':
                $title = $this->translator->trans('tournament.ended.title', $gameUpdate->getParameters(), 'game_updates');
                $description = $this->translator->trans('tournament.ended.description', $gameUpdate->getParameters(), 'game_updates');
                $message = $message = sprintf('%s %s', $title, $description);
                break;
            default:
                $titleKey = $gameUpdate->getTitle();
                $descriptionKey = $gameUpdate->getDescription();
                $title = $this->translator->trans($titleKey, $gameUpdate->getParameters(), 'game_updates');
                $description = $this->translator->trans($descriptionKey, $gameUpdate->getParameters(), 'game_updates');
                $message = sprintf('%s %s', $title, $description);
                break;
        }
        
        try {
            $this->service->postMessage($message, $group);
        } catch (\Exception $exception) {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $this->logger->error($message);
        }*/
    }
}
