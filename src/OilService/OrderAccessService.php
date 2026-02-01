<?php

declare(strict_types=1);

namespace App\OilService;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Entity\Order;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderAccessService
{
    public function assertUserHasAccessToOrder(Order $order, AuthUser $user): void
    {
        $route = $order->getRoute();

        if ($route === null) {
            throw new AccessDeniedHttpException('Order has no assigned route.');
        }

        foreach ($route->getRouteUsers() as $routeUser) {
            if ($routeUser->getUser()->getId()->__toString() === $user->getId()->__toString()) {
                return;
            }
        }

        throw new AccessDeniedHttpException('User does not have access to this order.');
    }
}
