<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
 * @Table(name="sms77_config")
 */
class Config implements ResourceInterface, TranslatableInterface {
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @Column(type="string")
     * @var string $apiKey
     */
    protected $apiKey;
    /**
     * @Column(type="string", nullable=true)
     * @var string $name
     */
    protected $name;
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
    protected $onShipping = false;
    /**
     * @Column(type="boolean")
     * @var bool $enabled
     */
    protected $enabled = true;
    /**
     * @Column(type="boolean")
     * @var bool $debug
     */
    protected $debug = false;
    /**
     * @Column(type="boolean")
     * @var bool $flash
     */
    protected $flash = false;
    /**
     * @Column(type="boolean", name="no_reload")
     * @var bool $noReload
     */
    protected $noReload = false;
    /**
     * @Column(type="boolean")
     * @var bool $utf8
     */
    protected $utf8 = false;
    /**
     * @Column(type="boolean")
     * @var bool $unicode
     */
    protected $unicode = false;
    /**
     * @Column(type="boolean", name="performance_tracking")
     * @var bool $performanceTracking
     */
    protected $performanceTracking = false;
    /**
     * @var Message[] $apiKey
     * @ORM\OneToMany(targetEntity="Message", mappedBy="config")
     */
    protected $messages;
    /**
     * @Column(type="string", nullable=true)
     * @var string $label
     */
    protected $label;
    /**
     * @Column(type="string", nullable=true)
     * @var string $delay
     */
    protected $delay;
    /**
     * @Column(type="string", nullable=true)
     * @var string $udh
     */
    protected $udh;

    public function __construct() {
        $this->initializeTranslationsCollection();

        $this->messages = new ArrayCollection();
    }

    public function __toString() {
        return $this->name ?? $this->id;
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

    public function getUdh(): ?string {
        return $this->udh;
    }

    public function setUdh(?string $udh): void {
        $this->udh = $udh;
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

    public function getDebug(): bool {
        return $this->debug;
    }

    public function setDebug(bool $debug): void {
        $this->debug = $debug;
    }

    public function getFlash(): bool {
        return $this->flash;
    }

    public function setFlash(bool $flash): void {
        $this->flash = $flash;
    }

    public function getNoReload(): bool {
        return $this->noReload;
    }

    public function setNoReload(bool $noReload): void {
        $this->noReload = $noReload;
    }

    public function getPerformanceTracking(): bool {
        return $this->performanceTracking;
    }

    public function setPerformanceTracking(bool $performanceTracking): void {
        $this->performanceTracking = $performanceTracking;
    }
    
    public function getUtf8(): bool {
        return $this->utf8;
    }

    public function setUtf8(bool $utf8): void {
        $this->utf8 = $utf8;
    }

    public function getUnicode(): bool {
        return $this->unicode;
    }

    public function setUnicode(bool $unicode): void {
        $this->unicode = $unicode;
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
    
    public function getApiParams() {
        return [
            'debug' => (int)$this->getDebug(),
            'delay' => $this->getDelay(),
            'flash' => (int)$this->getFlash(),
            'from' => $this->getTranslation()->getFrom(),
            'label' => $this->getLabel(),
            'no_reload' => (int)$this->getNoReload(),
            'performance_tracking' => (int)$this->getPerformanceTracking(),
            'udh' => $this->getUdh(),
            'unicode' => (int)$this->getUnicode(),
            'utf8' => (int)$this->getUtf8(),
        ];
    }

    /** {@inheritdoc} */
    protected function createTranslation(): ConfigTranslation {
        return new ConfigTranslation();
    }
}