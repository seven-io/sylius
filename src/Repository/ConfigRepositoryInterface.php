<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Repository;

use Doctrine\ORM\EntityManager;
use Seven\SyliusPlugin\Entity\Config;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConfigRepositoryInterface extends RepositoryInterface {
    public function findEnabled(): ?Config;

    public function findByNot(string $field, $value): array;
}
