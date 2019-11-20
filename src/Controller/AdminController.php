<?php

declare(strict_types=1);

namespace Sms77\SyliusPlugin\Controller;

use Doctrine\ORM\EntityManager;
use Sms77\SyliusPlugin\Entity\Configuration;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sms77\SyliusPlugin\Form\Type\ConfigurationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminController extends AbstractController
{
    private function _getConfig(): Configuration {
        /* @var RepositoryInterface $repo */
        $repo = $this->get("sms77.repository.configuration");

        /* @var Configuration $config */
        $config = $repo->find(1);

        return $config;
    }

    public function submitAction(Request $request): Response
    {
        $translator = $this->get('translator');

        $config = $this->_getConfig();

        $form = $request->get("sms77_configuration");

        if (array_key_exists("apiKey", $form)) {
            $config->setApiKey($form["apiKey"]);
        }

        $apiKey = $config->getApiKey();
        if (!isset($apiKey)) {
            $this->addFlash("error", $translator->trans('sms77.dashboard.apiKeyNeeded'));
            return $this->dashboardAction();
        }

        if (array_key_exists("onShipping", $form)) {
            $config->setOnShipping((bool)$form["onShipping"]);
        }

        if (array_key_exists("translations", $form)) {
            foreach ($form["translations"] as $localeCode => $translations) {
                if (array_key_exists("shippingText", $translations)) {
                    $config->setCurrentLocale($localeCode);
                    $config->setShippingText($translations["shippingText"]);
                }
            }
        }

        /* @var EntityManager $manager */
        $manager = $this->get("sms77.manager.configuration");
        $manager->persist($config);
        $manager->flush();

        $this->addFlash("success", $translator->trans('sms77.dashboard.configUpdated'));

        return $this->dashboardAction();
    }

    public function dashboardAction(): Response
    {
        return $this->render('@Sms77SyliusPlugin/Admin/Dashboard/index.html.twig', [
            'form' => $this->createForm(ConfigurationType::class, $this->_getConfig())->createView(),
        ]);
    }
}
