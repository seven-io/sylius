<?php
declare(strict_types=1);

namespace Sms77\SyliusPlugin\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @Entity
 * @Table(name="sms77_configuration")
 */
class Configuration implements ResourceInterface, TranslatableInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * @Column(type="string", nullable=true)
     * @var string | null $apiKey
     */
    protected $apiKey;

    /**
     * @Column(type="integer")
     * @Id()
     * @GeneratedValue()
     * @var integer $id
     */
    protected $id;

    /**
     * @Column(type="boolean")
     * @var bool $onShipping
     */
    protected $onShipping;

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function setFrom(?string $from): void
    {
        $this->getTranslation()->setFrom($from);
    }

    public function getFrom(): ?string
    {
        return $this->getTranslation()->getFrom();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOnShipping(bool $onShipping): void
    {
        $this->onShipping = $onShipping;
    }

    public function getOnShipping(): ?bool
    {
        return $this->onShipping;
    }

    public function setShippingText(?string $shippingText): void
    {
        $this->getTranslation()->setShippingText($shippingText);
    }

    public function getShippingText(): ?string
    {
        return $this->getTranslation()->getShippingText();
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): ConfigurationTranslation
    {
        return new ConfigurationTranslation();
    }
}