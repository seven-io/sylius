<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @Entity
 * @Table(name="seven_config")
 */
class Config implements ResourceInterface, TranslatableInterface {
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @Column(type="string")
     */
    protected string $apiKey;
    /**
     * @Column(type="string", nullable=true)
     */
    protected string $name;
    /**
     * @Column(type="integer")
     * @Id()
     * @GeneratedValue()
     */
    protected int $id;
    /**
     * @Column(type="boolean")
     */
    protected bool $onShipping = false;
    /**
     * @Column(type="boolean")
     */
    protected bool $enabled = true;
    /**
     * @Column(type="boolean")
     */
    protected bool $flash = false;
    /**
     * @Column(type="boolean", name="performance_tracking")
     */
    protected bool $performanceTracking = false;
    /**
     * @var Message[] $messages
     * @ORM\OneToMany(targetEntity="Message", mappedBy="config")
     */
    protected array|ArrayCollection $messages;
    /**
     * @Column(type="string", nullable=true)
     */
    protected string $label;
    /**
     * @Column(type="string", nullable=true)
     */
    protected string $delay;
    /**
     * @Column(type="string", nullable=true)
     */
    protected string $udh;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected int $ttl;
    /**
     * @Column(type="string", nullable=true, name="foreign_id")
     */
    protected string $foreignId;

    public function __construct() {
        $this->initializeTranslationsCollection();

        $this->messages = new ArrayCollection;
    }

    public function __toString() {
        return $this->name ?? (string)$this->id;
    }

    public function getApiKey(): ?string {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void {
        $this->apiKey = $apiKey;
    }

    public function getDelay(): ?string {
        return $this->delay;
    }

    public function setDelay(?string $delay): void {
        $this->delay = $delay;
    }

    public function getForeignId(): ?string {
        return $this->foreignId;
    }

    public function setForeignId(?string $foreignId): void {
        $this->foreignId = $foreignId;
    }

    public function getUdh(): ?string {
        return $this->udh;
    }

    public function setUdh(?string $udh): void {
        $this->udh = $udh;
    }

    public function getTtl(): ?int {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): void {
        $this->ttl = $ttl;
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function setLabel(?string $label): void {
        $this->label = $label;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function setFrom(?string $from): void {
        $this->getTranslation()->setFrom($from);
    }

    public function getFrom(): ?string {
        return $this->getTranslation()->getFrom();
    }

    public function getId(): ?int {
        return $this->id;
    }

    /** @return Collection|Message[] */
    public function getMessages(): Collection {
        return $this->messages;
    }

    public function getOnShipping(): bool {
        return $this->onShipping;
    }

    public function setOnShipping(bool $onShipping): void {
        $this->onShipping = $onShipping;
    }

    public function getFlash(): bool {
        return $this->flash;
    }

    public function setFlash(bool $flash): void {
        $this->flash = $flash;
    }

    public function getPerformanceTracking(): bool {
        return $this->performanceTracking;
    }

    public function setPerformanceTracking(bool $performanceTracking): void {
        $this->performanceTracking = $performanceTracking;
    }

    public function getEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }

    public function setShippingText(?string $shippingText): void {
        $this->getTranslation()->setShippingText($shippingText);
    }

    public function getShippingText(): ?string {
        return $this->getTranslation()->getShippingText();
    }

    public function getSmsParams(): SmsParams {
        return (new SmsParams)
            ->setDelay($this->getDelay())
            ->setFlash($this->getFlash())
            ->setForeignId($this->getForeignId())
            ->setFrom($this->getFrom())
            ->setLabel($this->getLabel())
            ->setPerformanceTracking($this->getPerformanceTracking())
            ->setTtl($this->getTtl())
            ->setUdh($this->getUdh())
            ;
    }

    public function getVoiceParams(): VoiceParams {
        return (new VoiceParams)
            ->setFrom($this->getFrom())
            ;
    }

    /** {@inheritdoc} */
    protected function createTranslation(): ConfigTranslation {
        return new ConfigTranslation();
    }
}
