<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sms77\SyliusPlugin\Entity\Config;
use Sms77\SyliusPlugin\Entity\ConfigTranslation;

final class ConfigListener {
    /** @var EntityManager $configManager */
    private $configManager;

    public function __construct(EntityManager $configManager) {
        $this->configManager = $configManager;
    }

    public function preUpdate(LifecycleEventArgs $event): void {
        $this->handleEvent($event);
    }

    private function handleEvent(LifecycleEventArgs $event): void {
        $config = $event->getEntity();
        if ($config instanceof ConfigTranslation) {
            $config = $config->getTranslatable();
        }

        /** @var Config $config */
        if (!$config instanceof Config || !$config->getEnabled()) {
            return;
        }

        $configRepo = $this->configManager->getRepository(Config::class);

        $configs = $config->getId()
            ? $configRepo->findByNot('id', $config->getId())
            : $configRepo->findAll();

        if (count($configs)) {
            foreach ($configs as $_config) {
                if (!$_config->getEnabled()) {
                    continue;
                }

                $_config->setEnabled(false);

                $this->configManager->persist($_config);
                $this->configManager->flush();
            }
        }
    }

    public function prePersist(LifecycleEventArgs $event): void {
        $this->handleEvent($event);
    }
}