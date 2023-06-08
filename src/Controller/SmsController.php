<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Controller;

use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;
use Seven\SyliusPlugin\Entity\Config;

class SmsController extends AbstractController {
    /**
     * @param Config $cfg
     * @return SmsParams
     */
    protected function buildParams(Config $cfg): SmsParams {
        $params = new SmsParams;

        $params->setDebug($cfg->getDebug());
        $params->setDelay($cfg->getDelay());
        $params->setFlash($cfg->getFlash());
        $params->setForeignId($cfg->getForeignId());
        $params->setFrom($cfg->getFrom());
        $params->setLabel($cfg->getLabel());
        $params->setNoReload($cfg->getNoReload());
        $params->setPerformanceTracking($cfg->getPerformanceTracking());
        $params->setTtl($cfg->getTtl());
        $params->setUdh($cfg->getUdh());
        $params->setUnicode($cfg->getUnicode());
        $params->setUtf8($cfg->getUtf8());

        return $params;
    }
}
