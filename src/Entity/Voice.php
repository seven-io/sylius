<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @Table(name="sms77_voice")
 */
class Voice extends AbstractMessage {
    /**
     * Many Message have Many Configs.
     * @ORM\ManyToMany(targetEntity="Sylius\Component\Customer\Model\CustomerGroup", fetch="EAGER")
     * @ORM\JoinTable(name="sms77_voice_messages_customer_groups",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="customer_group_id", referencedColumnName="id")}
     *      )
     */
    protected $customerGroups;

    /** @ORM\ManyToOne(targetEntity="Config", inversedBy="messages") */
    protected $config;
}
