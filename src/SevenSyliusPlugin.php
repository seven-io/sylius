<?php
declare(strict_types=1);

namespace Seven\SyliusPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SevenSyliusPlugin extends Bundle {
    use SyliusPluginTrait;
}
