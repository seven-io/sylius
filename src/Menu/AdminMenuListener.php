<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

class AdminMenuListener {
    /** @var UrlGeneratorInterface $router */
    private $router;
    /** @var DataCollectorTranslator $translator */
    private $translator;

    public function __construct(UrlGeneratorInterface $router, DataCollectorTranslator $translator) {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function addAdminMenuItems(MenuBuilderEvent $event): void {
        $menu = $event->getMenu();

        $newSubmenu = $menu
            ->addChild('seven')
            ->setLabel('seven');

        $newSubmenu
            ->addChild('seven-config')
            ->setLabel($this->translator->trans('seven.ui.configs'))
            ->setUri($this->router->generate('seven_admin_config_index'));

        $newSubmenu
            ->addChild('seven-write')
            ->setLabel($this->translator->trans('seven.ui.sms'))
            ->setUri($this->router->generate('seven_admin_sms_index'));

        $newSubmenu
            ->addChild('seven-write-voice')
            ->setLabel($this->translator->trans('seven.ui.text_to_speech'))
            ->setUri($this->router->generate('seven_admin_voice_index'));
    }
}
