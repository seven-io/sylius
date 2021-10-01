<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Controller;

use FOS\RestBundle\View\View;
use Sms77\Api\Client;
use Sms77\Api\Params\SmsParams;
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
        $configuration = $this->requestConfigurationFactory->create(
            $this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        /* @var Message $newResource */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $cfgId = $request->get('config');
        $cfgRepo = $this->get('sms77.repository.config');
        $newResource->setConfig(
            $cfgId ? $cfgRepo->find($cfgId) : $cfgRepo->findEnabled());

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST')
            && $form->handleRequest($request)->isValid()) {
            $newResource = $form->getData();

            $newResource->setResponse($this->_getResponse($newResource));

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

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(
                $configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(
            ResourceActions::CREATE, $configuration, $newResource);
        $initializeEventResponse = $initializeEvent->getResponse();
        $viewData = [
            'configuration' => $configuration,
            'form' => $form->createView(),
            'metadata' => $this->metadata,
            'resource' => $newResource,
            $this->metadata->getName() => $newResource,
        ];
        $tpl = $configuration->getTemplate(ResourceActions::CREATE . '.html');
        return $initializeEventResponse ?? new Response($this->container->get('twig')->render(
                $tpl,
                $viewData
            ));

        return $initializeEventResponse ?? $this->viewHandler->handle(
                $configuration,
                View::create()->setData($viewData)->setTemplate($tpl)
            );
    }

    private function getCustomerGroupIds(Message $message): array {
        $customerGroups = [];
        foreach ($message->getCustomerGroups()->toArray() as $customerGroup) {
            /** @var CustomerGroup $customerGroup */
            $customerGroups[] = $customerGroup->getId();
        }
        return $customerGroups;
    }

    private function _getResponse(Message $newResource): array {
        $customerGroupIds = $this->getCustomerGroupIds($newResource);
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
       // dd($isPersonalized);

        foreach ($customers as $customer) {
            $phone = $customer->getPhoneNumber() ?? '';

            if ('' === $phone) continue;

            if ($isPersonalized)
                $apiRequests[$phone] = str_replace('{0}', $customer->getFullName(), $text);
            else
                $apiRequests = [array_key_first($apiRequests) . ',' . $phone => $text];
        }

        $responses = [];
        $cfg = $newResource->getConfig();

        if (null !== $cfg) {
            $client = new Client($cfg->getApiKey(), 'sylius');

            $smsParams = new SmsParams;
            $smsParams->setDebug($cfg->getDebug());
            $smsParams->setDelay($cfg->getDelay());
            $smsParams->setFlash($cfg->getFlash());
            $smsParams->setForeignId($cfg->getForeignId());
            $smsParams->setFrom($cfg->getFrom());
            $smsParams->setLabel($cfg->getLabel());
            $smsParams->setNoReload($cfg->getNoReload());
            $smsParams->setPerformanceTracking($cfg->getPerformanceTracking());
            $smsParams->setTtl($cfg->getTtl());
            $smsParams->setUdh($cfg->getUdh());
            $smsParams->setUnicode($cfg->getUnicode());
            $smsParams->setUtf8($cfg->getUtf8());

            foreach ($apiRequests as $to => $text) {
                $smsParams = clone $smsParams;
                $smsParams->setText($text);
                $smsParams->setTo($to);
                $responses[] = $client->smsJson($smsParams);
            }
        }

        return $responses;
    }

    public function indexAction(Request $request): Response {
        $configuration = $this->requestConfigurationFactory->create(
            $this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        $resources = $this->resourcesCollectionProvider->get(
            $configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(
            ResourceActions::INDEX, $configuration, $resources);

        $view = View::create($resources);

        if (!$configuration->isHtmlRequest()) {
            $view->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->viewHandler->handle($configuration, $view);
        }

        $pluralName = $this->metadata->getPluralName();
        $tpl = $configuration->getTemplate(ResourceActions::INDEX . '.html');
        $viewData = [
            'configuration' => $configuration,
            'message_configurations' => $this->get('sms77.repository.config')->findAll(),
            'metadata' => $this->metadata,
            'resources' => $resources,
            $pluralName => $resources,
        ];
        return new Response($this->container->get('twig')->render(
            $tpl,
            $viewData
        ));

        if ($configuration->isHtmlRequest()) $view
            ->setTemplate($tpl)
            ->setTemplateVar($pluralName)
            ->setData($viewData);

        return $this->viewHandler->handle($configuration, $view);
    }
}
