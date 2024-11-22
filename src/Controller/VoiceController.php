<?php declare(strict_types=1);

namespace Seven\SyliusPlugin\Controller;

use Seven\Api\Resource\Voice\VoiceParams;
use Seven\SyliusPlugin\Entity\Config;

class VoiceController extends AbstractController {
    protected function buildParams(Config $cfg): VoiceParams {
        $params = new VoiceParams('', '');

        return $params;
    }
}
