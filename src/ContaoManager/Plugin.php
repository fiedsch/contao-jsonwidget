<?php

declare(strict_types=1);

/**
 * @author     Andreas Fieger
 */
namespace Fiedsch\JsonWidgetBundle\ContaoManager;

use Fiedsch\JsonWidgetBundle\JsonWidgetBundle;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(JsonWidgetBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

}
