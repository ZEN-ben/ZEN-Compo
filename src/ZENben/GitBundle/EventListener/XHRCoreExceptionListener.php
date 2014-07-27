<?php

namespace ZENben\AlertBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class XHRCoreExceptionListener
{

    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (! $request->isXmlHttpRequest()) {
            return;
        }
        $exception = $event->getException();
        
        $statusCode = $exception->getCode();
        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $content = [
            'type' => 'exception',
            'message' => [
                'type' => 'error',
                'content' => $exception->getMessage()
            ]
        ];
        $response = new JsonResponse($content, $statusCode);

        $event->setResponse($response);
    }
}
