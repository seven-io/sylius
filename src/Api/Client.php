<?php
declare(strict_types=1);

namespace Sms77\SyliusPlugin\Api;

use App\Entity\Shipping\Shipment;
use Sms77\Api\Client as ApiClient;
use Sms77\SyliusPlugin\Entity\Configuration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class Client
{
    /* @var RepositoryInterface $configurationRepo */
    protected $configurationRepo;

    /* @var Configuration $configuration */
    protected $configuration;

    public function __construct(RepositoryInterface $configurationRepo)
    {
        $this->configurationRepo = $configurationRepo;
        $this->configuration = $this->configurationRepo->find(1);
    }

    public function sms($data): void
    {
        $state = null;
        $to = null;
        $text = null;

        if ($data instanceof Shipment) {
            /* @var Shipment $data */
            $state = $data->getState();
            $to = $this->orderToPhone($data->getOrder());
            $text = $this->stateToText($state);
        }

        $client = $this->initApi();

        if (null !== $to && null !== $client) {
            $client->sms($to,  $text);
        }
    }

    private function stateToText(string $state): ?string {
        if (null !== $this->configuration && OrderShippingStates::STATE_SHIPPED === $state) {
            return $this->configuration->getShippingText();
        }

        return null;
    }

    private function initApi(): ?ApiClient {
        $client = null;

        if (null !== $this->configuration) {
            $apiKey = $this->configuration->getApiKey();

            if (isset($apiKey)) {
                $client = new ApiClient($this->configuration->getApiKey(), 'sylius');
            }
        }

        return $client;
    }

    private function orderToPhone(OrderInterface $order): ?string {
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        return null === $shippingAddress->getPhoneNumber()
            ? $billingAddress->getPhoneNumber()
            : $shippingAddress->getPhoneNumber();
    }
}