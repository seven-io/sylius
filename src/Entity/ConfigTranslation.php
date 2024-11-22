<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;

/**
 * @Entity
 * @Table(name="seven_config_translation")
 */
class ConfigTranslation extends AbstractTranslation implements ResourceInterface {
    /**
     * @Column(type="string", nullable=true, name="`from`")
     */
    private ?string $from;

    /**
     * @Column(type="integer")
     * @Id()
     * @GeneratedValue()
     */
    private int $id;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $shippingText;

    public function getFrom(): ?string {
        return $this->from;
    }

    public function setFrom(?string $from): void {
        $this->from = $from;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getShippingText(): ?string {
        return $this->shippingText;
    }

    public function setShippingText(?string $shippingText): void {
        $this->shippingText = $shippingText;
    }
}
