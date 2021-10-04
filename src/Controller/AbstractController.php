<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Controller;

use FOS\RestBundle\View\View;
use Sms77\Api\Client;
use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;
use Sms77\SyliusPlugin\Entity\AbstractMessage;
use Sms77\SyliusPlugin\Entity\Config;
use Sms77\SyliusPlugin\Entity\Message;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractController extends ResourceController {
    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response {
        $configuration = $this->requestConfigurationFactory->create(
            $this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        /* @var AbstractMessage $newResource */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $cfgId = $request->get('config');
        $cfgRepo = $this->get('sms77.repository.config');
        $newResource->setConfig(
            $cfgId ? $cfgRepo->find($cfgId) : $cfgRepo->findEnabled());

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST')
            && $form->handleRequest($request)->isValid()) {
            $newResource = $form->getData();

            $newResource->setResponse($this->getApiResponse($newResource));

            $event = $this->eventDispatcher->dispatchPreEvent(
                ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest())
                throw new HttpException($event->getErrorCode(), $event->getMessage());

            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                return $eventResponse ??
                    $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            if ($configuration->hasStateMachine())
                $this->stateMachine->apply($configuration, $newResource);

            $this->repository->add($newResource);

            if ($configuration->isHtmlRequest()) $this->flashHelper->addSuccessFlash(
                $configuration, ResourceActions::CREATE, $newResource);

            $postEvent = $this->eventDispatcher->dispatchPostEvent(
                ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) return $this->viewHandler->handle(
                $configuration, View::create($newResource, Response::HTTP_CREATED));

            $postEventResponse = $postEvent->getResponse();
            return $postEventResponse ??
                $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) return $this->viewHandler->handle(
            $configuration, View::create($form, Response::HTTP_BAD_REQUEST));

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(
            ResourceActions::CREATE, $configuration, $newResource);
        $initializeEventResponse = $initializeEvent->getResponse();

        return $initializeEventResponse ?? new Response($this->container->get('twig')->render(
                $configuration->getTemplate(ResourceActions::CREATE . '.html'),
                [
                    'configuration' => $configuration,
                    'form' => $form->createView(),
                    'metadata' => $this->metadata,
                    'resource' => $newResource,
                    $this->metadata->getName() => $newResource,
                ]
            ));
    }

    /**
     * @param AbstractMessage $newResource
     * @return array
     */
    private function getApiResponse(AbstractMessage $newResource): array {
        $customerGroupIds = $newResource->getCustomerGroupIds();
        /** @var CustomerRepository $customerRepo */
        $customerRepo = $this->manager->getRepository(Customer::class);
        $hasCustomerGroups = 0 !== count($customerGroupIds);
        /* @var CustomerInterface[] $customers */
        $customers = $hasCustomerGroups
            ? $customerRepo->findBy(['group' => $customerGroupIds])
            : $customerRepo->findAll();

        $text = $newResource->getMsg();
        $apiRequests = [];
        $isPersonalized = false !== strpos($newResource->getMsg(), '{0}');

        $isSMS = $newResource instanceof Message;

        foreach ($customers as $customer) {
            $phone = $customer->getPhoneNumber() ?? '';

            if ('' === $phone) continue;

            if ($isPersonalized || !$isSMS)
                $apiRequests[$phone] = str_replace('{0}', $customer->getFullName(), $text);
            else
                $apiRequests = [array_key_first($apiRequests) . ',' . $phone => $text];
        }

        $responses = [];
        $cfg = $newResource->getConfig();

        if (null !== $cfg) {
            $client = new Client($cfg->getApiKey(), 'sylius');

            $params = $this->buildParams($cfg);
            $params->setFrom($cfg->getFrom());

            foreach ($apiRequests as $to => $text) {
                $params = clone $params;
                $params->setText($text);
                $params->setTo($to);
                $responses[] = $client->{$isSMS ? 'smsJson' : 'voiceJson'}($params);
            }
        }

        return $responses;
    }

    /**
     * @param Config $cfg
     * @return SmsParams|VoiceParams
     */
    abstract protected function buildParams(Config $cfg): object;

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response {
        $configuration = $this->requestConfigurationFactory->create(
            $this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        $resources = $this->resourcesCollectionProvider->get(
            $configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(
            ResourceActions::INDEX, $configuration, $resources);

        if (!$configuration->isHtmlRequest()) return $this->viewHandler->handle(
            $configuration, View::create($resources, Response::HTTP_BAD_REQUEST));

        return new Response($this->container->get('twig')->render(
            $configuration->getTemplate(ResourceActions::INDEX . '.html'), [
                'configuration' => $configuration,
                'message_configurations' => $this->get('sms77.repository.config')->findAll(),
                'metadata' => $this->metadata,
                'resources' => $resources,
                $this->metadata->getPluralName() => $resources,
            ]
        ));
    }
}
