<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class AbstractMessage implements ResourceInterface {
    protected ArrayCollection $customerGroups;

    protected ?Config $config;

    /**
     * @Column(type="string")
     */
    protected string $msg = '';

    /**
     * @Column(type="string", nullable=true, name="`from`")
     */
    protected ?string $from = null;

    /**
     * @Column(type="json", nullable=true)
     */
    protected ?string $response;

    /**
     * @Column(type="integer")
     * @Id()
     * @GeneratedValue()
     */
    protected int $id;

    public function __construct() {
        $this->customerGroups = new ArrayCollection;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getResponse(): ?string {
        return $this->response;
    }

    public function setResponse(?string $response): void {
        $this->response = $response;
    }

    public function getFrom(): ?string {
        return $this->from;
    }

    public function setFrom(?string $from): void {
        $this->from = $from;
    }

    public function getMsg(): string {
        return $this->msg;
    }

    public function setMsg(string $msg): void {
        $this->msg = $msg;
    }

    public function getConfig(): ?Config {
        return $this->config;
    }

    public function setConfig(Config $config): void {
        $this->config = $config;
    }

    public function getCustomerGroupIds(): array {
        $customerGroups = [];

        foreach ($this->getCustomerGroups()->toArray() as $customerGroup) {
            /** @var CustomerGroup $customerGroup */
            $customerGroups[] = $customerGroup->getId();
        }

        return $customerGroups;
    }

    public function addCustomerGroup(CustomerGroup $customerGroup): void {
        if (!$this->customerGroups->contains($customerGroup)) {
            $this->customerGroups->add($customerGroup);
        }
    }

    public function removeCustomerGroup(CustomerGroup $customerGroup): bool {
        if ($this->customerGroups->contains($customerGroup)) {
            return $this->customerGroups->removeElement($customerGroup);
        }

        return false;
    }

    public function getCustomerGroups(): Collection {
        return $this->customerGroups;
    }

    public function setCustomerGroups(Collection $customerGroups): void {
        $this->customerGroups = $customerGroups;
    }
}
