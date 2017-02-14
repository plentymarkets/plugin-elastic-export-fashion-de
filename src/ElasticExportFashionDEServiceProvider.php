<?php

namespace ElasticExportFashionDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;

/**
 * Class ElasticExportFashionDEServiceProvider
 * @package ElasticExportFashionDE
 */
class ElasticExportFashionDEServiceProvider extends DataExchangeServiceProvider
{
    /**
     * Abstract function for registering the service provider.
     */
    public function register()
    {

    }

    /**
     * Adds the export format to the export container.
     *
     * @param ExportPresetContainer $container
     */
    public function exports(ExportPresetContainer $container)
    {
        $container->add(
            'FashionDE-Plugin',
            'ElasticExportFashionDE\ResultField\FashionDE',
            'ElasticExportFashionDE\Generator\FashionDE',
            'ElasticExportFashionDE\Filter\FashionDE',
            true
        );
    }
}