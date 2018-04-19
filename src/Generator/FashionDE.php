<?php

namespace ElasticExportFashionDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportPriceHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
use ElasticExport\Services\FiltrationService;
use ElasticExportFashionDE\Helper\PropertyHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueNameRepositoryContract;
use Plenty\Modules\Item\Attribute\Models\AttributeValueName;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchScrollRepositoryContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class FashionDE
 * @package ElasticExportFashionDE\Generator
 */
class FashionDE extends CSVPluginGenerator
{
	use Loggable;

	/**
	 * @var ElasticExportCoreHelper
	 */
	private $elasticExportCoreHelper;

	/**
	 * @var ElasticExportStockHelper
	 */
	private $elasticExportStockHelper;

	/**
	 * @var ElasticExportPriceHelper
	 */
	private $elasticExportPriceHelper;

	/**
	 * @var ArrayHelper
	 */
	private $arrayHelper;

	/**
	 * @var AttributeValueNameRepositoryContract
	 */
	private $attributeValueNameRepository;

	/**
	 * @var array PropertyHelper
	 */
	private $propertyHelper;

	/**
	 * @var array
	 */
	private $item;

    /**
     * @var FiltrationService
     */
    private $filtrationService;

	/**
	 * FashionDE constructor.
	 *
	 * @param ArrayHelper $arrayHelper
	 * @param AttributeValueNameRepositoryContract $attributeValueNameRepository ,
	 * @param PropertyHelper $propertyHelper
	 */
	public function __construct(
		ArrayHelper $arrayHelper,
		AttributeValueNameRepositoryContract $attributeValueNameRepository,
		PropertyHelper $propertyHelper)
	{
		$this->arrayHelper = $arrayHelper;
		$this->attributeValueNameRepository = $attributeValueNameRepository;
		$this->propertyHelper = $propertyHelper;
	}

	/**
	 * Generates and populates the data into the CSV file.
	 *
	 * @param VariationElasticSearchScrollRepositoryContract $elasticSearch
	 * @param array $formatSettings
	 * @param array $filter
	 */
	protected function generatePluginContent($elasticSearch, array $formatSettings = [], array $filter = [])
	{
		$this->elasticExportCoreHelper = pluginApp(ElasticExportCoreHelper::class);
		$this->elasticExportStockHelper = pluginApp(ElasticExportStockHelper::class);
		$this->elasticExportPriceHelper = pluginApp(ElasticExportPriceHelper::class);

		$settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');
		$this->filtrationService = pluginApp(FiltrationService::class, [$settings, $filter]);

		$this->setDelimiter("	");

		$this->addCSVContent([
			'art_nr',
			'art_name',
			'art_kurztext',
			'art_kategorie',
			'art_url',
			'art_img_url',
			'art_waehrung',
			'art_preis',
			'art_marke',
			'art_farbe',
			'art_groesse',
			'art_versand',
			'art_sale_preis',
			'art_geschlecht',
			'art_grundpreis',

		]);

		$lines = 0;
		$limitReached = false;
		$previousItemId = null;
		$currentItemId = null;

		if($elasticSearch instanceof VariationElasticSearchScrollRepositoryContract)
		{
			do
			{
				if($limitReached === true)
				{
					break;
				}

				$resultList = $elasticSearch->execute();

				if(count($resultList['error']) > 0)
				{
					$this->getLogger(__METHOD__)->error('ElasticExportFashionDE::log.occurredElasticSearchErrors', [
						'error message' => $resultList['error'],
					]);
				}

				if(is_array($resultList['documents']) && count($resultList['documents']) > 0)
				{
					foreach($resultList['documents'] as $variation)
					{
						if($lines == $filter['limit'])
						{
							$limitReached = true;
							break;
						}

						if($this->filtrationService->filter($variation))
						{
							continue;
						}

						try
						{
							$currentItemId = $variation['data']['item']['id'];

							if(is_null($previousItemId) || $currentItemId == $previousItemId)
							{
								$this->variationGrouper($variation, $settings);
								$previousItemId = $currentItemId;
							}
							elseif(!is_null($previousItemId) && $currentItemId != $previousItemId)
							{
								foreach($this->item as $data)
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

								unset($this->item);

								$this->variationGrouper($variation, $settings);
								$previousItemId = $currentItemId;
							}
							$lines = $lines +1;
						}
						catch(\Throwable $exception)
						{
							$this->getLogger(__METHOD__)->error('ElasticExportFashionDE::log.buildRowError', [
								'error' => $exception->getMessage(),
								'line' => $exception->getLine(),
							]);
						}
					}

					//add the last batch
					foreach($this->item as $data)
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
			} while ($elasticSearch->hasNext());
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
        $priceList = $this->getPriceList($variation, $settings);

		// Get shipping costs
		$shippingCost = $this->elasticExportCoreHelper->getShippingCost($variation['data']['item']['id'], $settings);
		if(is_null($shippingCost))
		{
			$shippingCost = '';
		}

		$data = [
			'art_nr'            => $variation['id'],
			'art_name'          => $this->elasticExportCoreHelper->getMutatedName($variation, $settings),
			'art_kurztext'      => $this->elasticExportCoreHelper->getMutatedDescription($variation, $settings, 3000),
			'art_kategorie'     => $this->elasticExportCoreHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
			'art_url'           => $this->elasticExportCoreHelper->getMutatedUrl($variation, $settings),
			'art_img_url'       => $this->elasticExportCoreHelper->getMainImage($variation, $settings),
			'art_waehrung'      => $priceList['currency'],
			'art_preis'         => $priceList['price'],
			'art_marke'         => substr(trim($this->elasticExportCoreHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id'])), 0, 20),
			'art_farbe'         => [],
			'art_groesse'       => [],
			'art_versand'       => $shippingCost,
			'art_sale_preis'    => $priceList['salePrice'],
			'art_geschlecht'    => $this->propertyHelper->getPropertyValueByBackendName($variation, 'article_gender'),
			'art_grundpreis'    => $priceList['basePrice'],
		];

		return $data;
	}

    /**
     * @param  array    $variation
     * @param  KeyValue $settings
     * @return array
     */
    private function getPriceList(array $variation, KeyValue $settings):array
    {
        $price = $salePrice = $basePrice = '';

        $priceList = $this->elasticExportPriceHelper->getPriceList($variation, $settings, 2, ',');

        //determinate which price to use as 'art_preis'
        //only use rrp if it is higher than the normal price
        if($priceList['recommendedRetailPrice'] > 0.00 && $priceList['recommendedRetailPrice'] > $priceList['price'])
        {
            $price = $priceList['recommendedRetailPrice'];
        }
        elseif($priceList['price'] > 0.00)
        {
            $price = $priceList['price'];
        }

        //determinate which price to use as 'art_sale_preis'
        //only use specialPrice if it is set and the lowest price available
        if($priceList['specialPrice'] > 0.00 && $priceList['specialPrice'] < $price && $priceList['specialPrice'] < $priceList['price'])
        {
            $salePrice = $priceList['specialPrice'];
        }
        elseif($priceList['price'] > 0.00 && $priceList['price'] < $price)
        {
            $salePrice = $priceList['price'];
        }

        //determinate with which price the base price has to be calculated
        if($salePrice > 0.00)
        {
            $basePrice = $this->elasticExportPriceHelper->getBasePrice($variation, (float)$salePrice, $settings->get('lang'));
        }
        elseif($price > 0.00)
        {
            $basePrice = $this->elasticExportPriceHelper->getBasePrice($variation, (float)$price, $settings->get('lang'));
        }

        return [
            'price'     => $price,
            'salePrice' => $salePrice,
            'currency'  => $priceList['currency'],
            'basePrice' => $basePrice,
        ];
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
	 * @param $variation
	 * @param $settings
	 */
	private function variationGrouper($variation, $settings)
	{
		if(!array_key_exists($variation['data']['item']['id'], $this->item))
		{
			$this->item[$variation['data']['item']['id']] = $this->getMain($variation, $settings);
		}

		if(array_key_exists($variation['data']['item']['id'], $this->item) && count($variation['data']['attributes']) > 0)
		{
			$variationAttributes = $this->getVariationAttributes($variation, $settings);

			if(array_key_exists('Color', $variationAttributes))
			{
				$this->item[$variation['data']['item']['id']]['art_farbe'] = array_unique(array_merge($this->item[$variation['data']['item']['id']]['art_farbe'], $variationAttributes['Color']));
			}

			if(array_key_exists('Size', $variationAttributes))
			{
				$this->item[$variation['data']['item']['id']]['art_groesse'] = array_unique(array_merge($this->item[$variation['data']['item']['id']]['art_groesse'], $variationAttributes['Size']));
			}
		}
	}
}