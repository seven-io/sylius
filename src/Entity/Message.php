<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @Table(name="seven_message")
 */
class Message extends AbstractMessage {
    /** @ORM\ManyToOne(targetEntity="Config", inversedBy="messages") */
    protected ?Config $config;

    /**
     * Many Message have Many Configs.
     * @ORM\ManyToMany(targetEntity="Sylius\Component\Customer\Model\CustomerGroup", fetch="EAGER")
     * @ORM\JoinTable(name="seven_messages_customer_groups",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="customer_group_id", referencedColumnName="id")}
     *      )
     */
    protected \Doctrine\Common\Collections\ArrayCollection $customerGroups;
}
