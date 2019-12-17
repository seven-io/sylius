<?php
declare(strict_types=1);

namespace Sms77\SyliusPlugin\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sms77\SyliusPlugin\Entity\Configuration;
use Sms77\SyliusPlugin\Entity\ConfigurationTranslation;

class ConfigurationFixtures implements FixtureInterface
{
    private function makeTranslation(string $text, string $localeCode): ConfigurationTranslation {
        $translation = new ConfigurationTranslation();
        $translation->setShippingText($text);
        $translation->setLocale($localeCode);
        return $translation;
    }

    public function load(ObjectManager $manager)
    {
        $conf = new Configuration();

        $conf->setOnShipping(false);

        $conf->addTranslation($this->makeTranslation('Your order has just been handed over to our logistics partner. It should already arrive in a few days. Best regards!', 'en_US'));
        $conf->addTranslation($this->makeTranslation('Ihre Bestellung wurde soeben an unseren Logistikpartner übergeben. In wenigen Tagen schon sollte Ihre Bestellung bei Ihnen eintreffen. Beste Grüße!', 'de_DE'));

        $manager->persist($conf);
        $manager->flush();
    }
}