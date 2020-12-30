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
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @Entity
 * @Table(name="sms77_message")
 */
class Message implements ResourceInterface {
    /**
     * @Column(type="string")
     * @var string $msg
     */
    protected $msg = '';

    /** @ORM\ManyToOne(targetEntity="Config", inversedBy="messages") */
    protected $config;

    /**
     * @Column(type="string")
     * @var string $from
     */
    protected $from = '';

    /**
     * @Column(type="json", nullable=true)
     * @var string | null $response
     */
    protected $response;

    /**
     * @Column(type="integer")
     * @Id()
     * @GeneratedValue()
     * @var int $id
     */
    protected $id;

    /**
     * Many Message have Many Configs.
     * @ORM\ManyToMany(targetEntity="Sylius\Component\Customer\Model\CustomerGroup", fetch="EAGER")
     * @ORM\JoinTable(name="sms77_messages_customer_groups",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="customer_group_id", referencedColumnName="id")}
     *      )
     */
    protected $customerGroups;

    public function __construct() {
        $this->customerGroups = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
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

    public function getResponse() {
        return $this->response;
    }

    public function setResponse($response): void {
        $this->response = $response;
    }

    public function getFrom(): string {
        return $this->from;
    }

    public function setFrom(string $from): void {
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
}