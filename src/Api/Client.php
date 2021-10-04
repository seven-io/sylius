<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Api;

use Sms77\Api\Client as ApiClient;
use Sms77\SyliusPlugin\Entity\Config;
use Sms77\SyliusPlugin\Repository\ConfigRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;

class Client {
    /* @var Config $configuration */
    protected $configuration;

    public function __construct(ConfigRepository $configurationRepo) {
        $this->configuration = $configurationRepo->findEnabled();
    }

    public function sms($data): void {
        $state = null;
        $to = null;
        $text = null;

        if ($data instanceof ShipmentInterface) {
            $state = $data->getState();
            $to = $this->orderToPhone($data->getOrder());
            $text = $this->stateToText($state);
        }

        $client = $this->initApi();

        if (null === $to || null === $client) return;

        $params = $this->configuration->getSmsParams()
            ->setText($text)
            ->setTo($to);

        $client->sms($params);
    }

    private function orderToPhone(OrderInterface $order): ?string {
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        if (null !== $shippingAddress && null !== $shippingAddress->getPhoneNumber())
            return $shippingAddress->getPhoneNumber();

        if (null !== $billingAddress && null !== $billingAddress->getPhoneNumber())
            return $billingAddress->getPhoneNumber();

        return null;
    }

    private function stateToText(string $state): ?string {
        if (null !== $this->configuration
            && OrderShippingStates::STATE_SHIPPED === $state)
            return $this->configuration->getShippingText();

        return null;
    }

    private function initApi(): ?ApiClient {
        $client = null;

        if (null !== $this->configuration) {
            $apiKey = $this->configuration->getApiKey();

            if (isset($apiKey)) $client = new ApiClient($apiKey, 'sylius');
        }

        return $client;
    }
}
