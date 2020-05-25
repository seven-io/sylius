<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Controller;

use FOS\RestBundle\View\View;
use Sms77\Api\Client;
use Sms77\SyliusPlugin\Entity\Message;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MessageController extends ResourceController {
    public function createAction(Request $request): Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        /* @var Message $newResource */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $cfgId = $request->get('config');
        $cfgRepo = $this->get('sms77.repository.config');
        $newResource->setConfig($cfgId ? $cfgRepo->find($cfgId) : $cfgRepo->findEnabled());

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newResource = $form->getData();

            $newResource->setResponse($this->_getResponse($newResource));

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                if (null !== $eventResponse) {
                    return $eventResponse;
                }

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            if ($configuration->hasStateMachine()) {
                $this->stateMachine->apply($configuration, $newResource);
            }

            $this->repository->add($newResource);

            if ($configuration->isHtmlRequest()) {
                $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($newResource, Response::HTTP_CREATED));
            }

            $postEventResponse = $postEvent->getResponse();
            if (null !== $postEventResponse) {
                return $postEventResponse;
            }

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::CREATE, $configuration, $newResource);
        $initializeEventResponse = $initializeEvent->getResponse();
        if (null !== $initializeEventResponse) {
            return $initializeEventResponse;
        }

        return $this->viewHandler->handle(
            $configuration,
            View::create()
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $newResource,
                    $this->metadata->getName() => $newResource,
                    'form' => $form->createView(),
                ])
                ->setTemplate($configuration->getTemplate(ResourceActions::CREATE . '.html'))
        );
    }

    private function _getResponse(Message $newResource): array {
        $customerGroups = [];
        foreach ($newResource->getCustomerGroups()->toArray() as $customerGroup) {
            /** @var CustomerGroup $customerGroup */
            $customerGroups[] = $customerGroup->getId();
        }

        /** @var CustomerRepository $customerRepo */
        $customerRepo = $this->manager->getRepository(Customer::class);
        $hasCustomerGroups = 0 !== count($customerGroups);
        /* @var CustomerInterface[] $customers */
        $customers = $hasCustomerGroups ? $customerRepo->findBy(['group' => $customerGroups]) : $customerRepo->findAll();

        $text = $newResource->getMsg();
        $apiRequests = [];
        $isPersonalized = false !== strpos($newResource->getMsg(), '{0}');
        foreach ($customers as $customer) {
            $phone = $customer->getPhoneNumber() ?? '';

            if (!mb_strlen($phone)) {
                continue;
            }

            $apiRequests[$phone] = $isPersonalized
                ? str_replace('{0}', $customer->getFullName(), $text)
                : $text;
        }

        $client = new Client($newResource->getConfig()->getApiKey(), 'sylius');
        $responses = [];
        foreach ($apiRequests as $to => $text) {
            $responses[] = $client->sms($to, $text, ['json' => 1]);
        }

        return $responses;
    }

    public function indexAction(Request $request): Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, $resources);

        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX . '.html'))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    'message_configurations' => $this->get('sms77.repository.config')->findAll(),
                    $this->metadata->getPluralName() => $resources,
                ]);
        }

        return $this->viewHandler->handle($configuration, $view);
    }
}