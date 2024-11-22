<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Controller;

use Sms77\Api\Params\VoiceParams;
use Seven\SyliusPlugin\Entity\Config;

class VoiceController extends AbstractController {
    protected function buildParams(Config $cfg): VoiceParams {
        $params = new VoiceParams;

        $params->setXml($cfg->getXml());

        return $params;
    }
}
