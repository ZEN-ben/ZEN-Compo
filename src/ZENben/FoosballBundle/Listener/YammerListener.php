<?php

namespace ZENben\FoosballBundle\Listener;

use ZENben\Bundle\YammerBundle\Service\YammerService;
use ZENben\FoosballBundle\Event\GameUpdateEvent;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

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
    
    public function __construct(YammerService $service, Translator $translator) {
        $this->service = $service;
        $this->translator = $translator;
    }
    
    public function onGameUpdate(GameUpdateEvent $event) {
        $gameUpdate = $event->getGameUpdate();
        $group = $gameUpdate->getGame()->getYammerGroup();
        
        switch ($gameUpdate->getType()) {
            case 'match.updated':
                $message = $this->translator->trans('p1.won.agianst.p2', $gameUpdate->getParameters(), 'game_updates');
                break;
            default:
                $titleKey = $gameUpdate->getTitle();
                $descriptionKey = $gameUpdate->getDescription();
                $title = $this->translator->trans($titleKey, $gameUpdate->getParameters(), 'game_updates');
                $description = $this->translator->trans($descriptionKey, $gameUpdate->getParameters(), 'game_updates');
                $message = sprintf('%s, %s', $title, $description);
                break;
        }
        
        $this->service->postMessage($message, $group);
    }
    
}