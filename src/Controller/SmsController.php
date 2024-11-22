<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Controller;

use Seven\Api\Resource\Sms\SmsParams;
use Seven\SyliusPlugin\Entity\Config;

class SmsController extends AbstractController {
    protected function buildParams(Config $cfg): SmsParams {
        $params = new SmsParams('', '');

        $params->setDelay($cfg->getDelay());
        $params->setFlash($cfg->getFlash());
        $params->setForeignId($cfg->getForeignId());
        $params->setFrom($cfg->getFrom());
        $params->setLabel($cfg->getLabel());
        $params->setPerformanceTracking($cfg->getPerformanceTracking());
        $params->setTtl($cfg->getTtl());
        $params->setUdh($cfg->getUdh());

        return $params;
    }
}
