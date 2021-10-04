<?php declare(strict_types=1);

namespace Sms77\SyliusPlugin\Controller;

use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;
use Sms77\SyliusPlugin\Entity\Config;

class VoiceController extends AbstractController {
    /**
     * @param Config $cfg
     * @return VoiceParams
     */
    protected function buildParams(Config $cfg): VoiceParams {
        $params = new VoiceParams;

        $params->setXml($cfg->getXml());

        return $params;
    }
}
