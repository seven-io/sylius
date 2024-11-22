<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Seven\SyliusPlugin\Entity\Config;
use Seven\SyliusPlugin\Entity\ConfigTranslation;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
final class ConfigListener {
    public function __construct(private EntityManager $configManager) {
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    private function handleEvent(LifecycleEventArgs $event): void {
        $config = $event->getEntity();
        if ($config instanceof ConfigTranslation) $config = $config->getTranslatable();

        /** @var Config $config */
        if (!$config instanceof Config || !$config->getEnabled()) return;

        $configRepo = $this->configManager->getRepository(Config::class);

        $configs = $config->getId()
            ? $configRepo->findByNot('id', $config->getId())
            : $configRepo->findAll();

        if (count($configs)) {
            foreach ($configs as $_config) {
                if (!$_config->getEnabled()) continue;

                $_config->setEnabled(false);

                $this->configManager->persist($_config);
                $this->configManager->flush();
            }
        }
    }
}
