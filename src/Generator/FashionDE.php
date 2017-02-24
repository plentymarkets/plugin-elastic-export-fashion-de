<?php

namespace ElasticExportFashionDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueNameRepositoryContract;
use Plenty\Modules\Item\Attribute\Models\AttributeValueName;


/**
 * Class FashionDE
 * @package ElasticExportFashionDE\Generator
 */
class FashionDE extends CSVPluginGenerator
{
    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportCoreHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var AttributeValueNameRepositoryContract
     */
    private $attributeValueNameRepository;

    /**
     * @var array
     */
    private $idlVariations = array();


    /**
     * FashionDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param AttributeValueNameRepositoryContract $attributeValueNameRepository,
     */
    public function __construct(ArrayHelper $arrayHelper, AttributeValueNameRepositoryContract $attributeValueNameRepository)
    {
        $this->arrayHelper = $arrayHelper;
        $this->attributeValueNameRepository = $attributeValueNameRepository;
    }

    /**
     * Generates and populates the data into the CSV file.
     *
     * @param array $resultData
     * @param array $formatSettings
     * @param array $filter
     */
    protected function generatePluginContent($resultData, array $formatSettings = [], array $filter = [])
    {
        $this->elasticExportCoreHelper = pluginApp(ElasticExportCoreHelper::class);

        if(is_array($resultData) && count($resultData['documents']) > 0)
        {
            $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

            $this->setDelimiter(" ");

            $this->addCSVContent([
                'art_nr',
                'art_name',
                'art_kurztext',
                'art_kategorie',
                'art_url',
                'art_img_url',
                'waehrung',
                'art_preis',
                'art_marke',
                'art_farbe',
                'art_groesse',
                'art_versand',
                'art_sale_preis',
                'art_geschlecht',
                'art_grundpreis',

            ]);

            // Create a List with all VariationIds
            $variationIdList = array();
            foreach($resultData['documents'] as $variation)
            {
                $variationIdList[] = $variation['id'];
            }

            // Get the missing ES fields from IDL(ItemDataLayer)
            if(is_array($variationIdList) && count($variationIdList) > 0)
            {
                /**
                 * @var \ElasticExportFashionDE\IDL_ResultList\FashionDE $idlResultList
                 */
                $idlResultList = pluginApp(\ElasticExportFashionDE\IDL_ResultList\FashionDE::class);
                $idlResultList = $idlResultList->getResultList($variationIdList, $settings, $filter);
            }

            // Creates an array with the variationId as key to surpass the sorting problem
            if(isset($idlResultList) && $idlResultList instanceof RecordList)
            {
                $this->createIdlArray($idlResultList);
            }

            $rows = [];

            foreach($resultData['documents'] as $variation)
            {
                if(!array_key_exists($variation['id'], $rows))
                {
                    $rows[$variation['id']] = $this->getMain($variation, $settings);
                }

                if(array_key_exists($variation['id'], $rows) && count($variation['data']['attributes']) > 0)
                {
                    $variationAttributes = $this->getVariationAttributes($variation, $settings);

                    if(array_key_exists('Color', $variationAttributes))
                    {
                        $rows[$variation['id']]['art_farbe'] = array_unique(array_merge($rows[$variation['id']]['art_farbe'], $variationAttributes['Color']));
                    }

                    if(array_key_exists('Size', $variationAttributes))
                    {
                        $rows[$variation['id']]['art_groesse'] = array_unique(array_merge($rows[$variation['id']]['art_groesse'], $variationAttributes['Size']));
                    }
                }
            }

            foreach($rows as $data)
            {
                if(array_key_exists('art_farbe', $data) && is_array($data['art_farbe']))
                {
                    $data['art_farbe'] = implode(', ', $data['art_farbe']);
                }

                if(array_key_exists('art_groesse', $data) && is_array($data['art_groesse']))
                {
                    $data['art_groesse'] = implode(', ', $data['art_groesse']);
                }

                $this->addCSVContent(array_values($data));
            }
        }
    }

    /**
     * Get main information.
     *
     * @param  array $variation
     * @param  KeyValue $settings
     * @return array
     */
    private function getMain($variation, KeyValue $settings):array
    {
        // Get and set the price and rrp
        $price = $this->idlVariations[$variation['id']]['variationRetailPrice.price'];
        $rrp = $this->elasticExportCoreHelper->getRecommendedRetailPrice($this->idlVariations[$variation['id']]['variationRecommendedRetailPrice.price'], $settings);

        $price = $rrp > $price ? $rrp : $price;
        $rrp = $rrp > $price ? $price : $rrp;

        // Get shipping costs
        $shippingCost = $this->elasticExportCoreHelper->getShippingCost($variation['data']['item']['id'], $settings);
        if(is_null($shippingCost))
        {
            $shippingCost = '';
        }

        $data = [
            'art_nr'            => $variation['id'],
            'art_name'          => $this->elasticExportCoreHelper->getName($variation, $settings),
            'art_kurztext'      => $this->elasticExportCoreHelper->getDescription($variation, $settings, 3000),
            'art_kategorie'     => $this->elasticExportCoreHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
            'art_url'           => $this->elasticExportCoreHelper->getUrl($variation, $settings),
            'art_img_url'       => $this->elasticExportCoreHelper->getMainImage($variation, $settings),
            'waehrung'          => $this->idlVariations[$variation['id']]['variationRetailPrice.currency'],
            'art_preis'         => number_format((float)$price, 2, ',', ''),
            'art_marke'         => substr(trim($this->elasticExportCoreHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id'])), 0, 20),
            'art_farbe'         => [],
            'art_groesse'       => [],
            'art_versand'       => $shippingCost,
            'art_sale_preis'    => number_format((float)$rrp, 2, ',', ''),
            'art_geschlecht'    => $this->elasticExportCoreHelper->getItemCharacterByBackendName($this->idlVariations[$variation['id']], $settings, 'article_gender'),
            'art_grundpreis'    => $this->elasticExportCoreHelper->getBasePrice($variation, $this->idlVariations[$variation['id']], $settings->get('lang'), '/', false, false, '', $rrp > 0 ? $rrp : $price),
        ];

        return $data;
    }

    /**
     * Get variation attributes.
     *
     * @param  array $variation
     * @param  KeyValue $settings
     * @return array<string,string>
     */
    private function getVariationAttributes($variation, KeyValue $settings):array
    {
        $variationAttributes = [];

        foreach($variation['data']['attributes'] as $variationAttribute)
        {
            $attributeValueName = $this->attributeValueNameRepository->findOne($variationAttribute['valueId'], $settings->get('lang'));

            if($attributeValueName instanceof AttributeValueName)
            {
                if($attributeValueName->attributeValue->attribute->amazonAttribute)
                {
                    $variationAttributes[$attributeValueName->attributeValue->attribute->amazonAttribute][] = $attributeValueName->name;
                }
            }
        }

        return $variationAttributes;
    }

    /**
     * Creates an array with the rest of data needed from the ItemDataLayer.
     *
     * @param RecordList $idlResultList
     */
    private function createIdlArray($idlResultList)
    {
        if($idlResultList instanceof RecordList)
        {
            foreach($idlResultList as $idlVariation)
            {
                if($idlVariation instanceof Record)
                {
                    $this->idlVariations[$idlVariation->variationBase->id] = [
                        'itemBase.id' => $idlVariation->itemBase->id,
                        'variationBase.id' => $idlVariation->variationBase->id,
                        'itemPropertyList' => $idlVariation->itemPropertyList,
                        'variationRetailPrice.price' => $idlVariation->variationRetailPrice->price,
                        'variationRetailPrice.currency' => $idlVariation->variationRetailPrice->currency,
                        'variationRecommendedRetailPrice.price' => $idlVariation->variationRecommendedRetailPrice->price,
                    ];
                }
            }
        }
    }
}