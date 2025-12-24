<?php

declare(strict_types=1);

namespace App\Auth\EventListener;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener for the REQUEST event. Patches the HeaderBag because the
 * "Authorization" header is not included in $_SERVER
 */
class AuthenticationHeaderListener
{
    /**
     * Handles REQUEST event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->fixAuthHeader($event->getRequest()->headers);
    }

    /**
     * PHP does not include HTTP_AUTHORIZATION in the $_SERVER array, so this header is missing.
     * We retrieve it from apache_request_headers()
     */
    protected function fixAuthHeader(HeaderBag $headers): void
    {
        if (function_exists('apache_request_headers') && !$headers->has('Authorization')) {
            $all = apache_request_headers();
            if (isset($all['Authorization'])) {
                $headers->set('Authorization', $all['Authorization']);
            }
        }
    }
}
