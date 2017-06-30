<?php

namespace ElasticExportFashionDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportPriceHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
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
	private $group;

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

						if($this->elasticExportStockHelper->isFilteredByStock($variation, $filter) === true)
						{
							continue;
						}

						try
						{
							$currentItemId = $variation['data']['item']['id'];

							if(is_null($previousItemId) || $currentItemId == $previousItemId)
							{
								$this->buildGroup($variation, $settings);
								$previousItemId = $currentItemId;
							}
							elseif(!is_null($previousItemId) && $currentItemId != $previousItemId)
							{
								foreach($this->group as $data)
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

								unset($this->group);

								$this->buildGroup($variation, $settings);
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
		$priceList = $this->elasticExportPriceHelper->getPriceList($variation, $settings, 2, ',');
		$rrp = $priceList['recommendedRetailPrice'];
		$price = $priceList['price'];

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
			'art_name'          => $this->elasticExportCoreHelper->getMutatedName($variation, $settings),
			'art_kurztext'      => $this->elasticExportCoreHelper->getMutatedDescription($variation, $settings, 3000),
			'art_kategorie'     => $this->elasticExportCoreHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
			'art_url'           => $this->elasticExportCoreHelper->getMutatedUrl($variation, $settings),
			'art_img_url'       => $this->elasticExportCoreHelper->getMainImage($variation, $settings),
			'waehrung'          => $priceList['currency'],
			'art_preis'         => number_format((float)$price, 2, ',', ''),
			'art_marke'         => substr(trim($this->elasticExportCoreHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id'])), 0, 20),
			'art_farbe'         => [],
			'art_groesse'       => [],
			'art_versand'       => $shippingCost,
			'art_sale_preis'    => $rrp,
			'art_geschlecht'    => $this->propertyHelper->getPropertyValueByBackendName($variation, 'article_gender'),
			'art_grundpreis'    => $this->elasticExportPriceHelper->getBasePrice($variation, (float)$price, $settings->get('lang')),
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

	private function buildGroup($variation, $settings)
	{
		if(!array_key_exists($variation['data']['item']['id'], $this->group))
		{
			$this->group[$variation['data']['item']['id']] = $this->getMain($variation, $settings);
		}

		if(array_key_exists($variation['data']['item']['id'], $this->group) && count($variation['data']['attributes']) > 0)
		{
			$variationAttributes = $this->getVariationAttributes($variation, $settings);

			if(array_key_exists('Color', $variationAttributes))
			{
				$this->group[$variation['data']['item']['id']]['art_farbe'] = array_unique(array_merge($this->group[$variation['data']['item']['id']]['art_farbe'], $variationAttributes['Color']));
			}

			if(array_key_exists('Size', $variationAttributes))
			{
				$this->group[$variation['data']['item']['id']]['art_groesse'] = array_unique(array_merge($this->group[$variation['data']['item']['id']]['art_groesse'], $variationAttributes['Size']));
			}
		}
	}
}