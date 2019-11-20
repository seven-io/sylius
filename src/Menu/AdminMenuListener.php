<?php
declare(strict_types=1);

namespace Sms77\SyliusPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $newSubmenu = $menu
            ->addChild('new')
            ->setLabel('Sms77')
        ;

        $newSubmenu
            ->addChild('new-subitem')
            ->setLabel('Dashboard')
            ->setUri('/admin/sms77/dashboard')
        ;
    }
}