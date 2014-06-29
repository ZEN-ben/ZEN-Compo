<?php

namespace ZENben\GitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$debug = [];
    	$event = $this->getRequest()->headers->get('X-GitHub-Event');

		var_dump($this->getRequest()->query->get('ref'));

    	switch ($event) {
    		case 'push':
    			$debug = [
    				'ref' => $this->getRequest()->request->get('ref'),
    				'after',
    				'before'
    			];
    			break;
    	}

    	$jsonResponse = new JsonResponse([
             'status' => 'OK',
             'debug' => $debug
        ]);
        return $jsonResponse;
    }
}
