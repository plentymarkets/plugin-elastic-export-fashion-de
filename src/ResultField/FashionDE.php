<?php

namespace ElasticExportFashionDE\ResultField;

use Plenty\Modules\Cloud\ElasticSearch\Lib\Source\Mutator\BuiltIn\LanguageMutator;
use Plenty\Modules\DataExchange\Contracts\ResultFields;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\Search\Mutators\DefaultCategoryMutator;
use Plenty\Modules\Item\Search\Mutators\ImageMutator;
use Plenty\Modules\Item\Search\Mutators\KeyMutator;


/**
 * Class FashionDE
 * @package ElasticExportFashionDE\ResultField
 */
class FashionDE extends ResultFields
{
	/**
	 * @var ArrayHelper
	 */
	private $arrayHelper;


	/**
	 * FashionDE constructor.
	 *
	 * @param ArrayHelper $arrayHelper
	 */
	public function __construct(ArrayHelper $arrayHelper)
	{
		$this->arrayHelper = $arrayHelper;
	}

	/**
	 * Creates the fields set to be retrieved from ElasticSearch.
	 *
	 * @param array $formatSettings
	 * @return array
	 */
	public function generateResultFields(array $formatSettings = []):array
	{
		$settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

		$reference = $settings->get('referrerId') ? $settings->get('referrerId') : -1;

		$this->setOrderByList(['variation.itemId', 'ASC']);

		$itemDescriptionFields = ['texts.urlPath'];

		$itemDescriptionFields[] = ($settings->get('nameId')) ? 'texts.name' . $settings->get('nameId') : 'texts.name1';

		if($settings->get('descriptionType') == 'itemShortDescription'
			|| $settings->get('previewTextType') == 'itemShortDescription')
		{
			$itemDescriptionFields[] = 'texts.shortDescription';
		}
		if($settings->get('descriptionType') == 'itemDescription'
			|| $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
			|| $settings->get('previewTextType') == 'itemDescription'
			|| $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
		{
			$itemDescriptionFields[] = 'texts.description';
		}
		if($settings->get('descriptionType') == 'technicalData'
			|| $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
			|| $settings->get('previewTextType') == 'technicalData'
			|| $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
		{
			$itemDescriptionFields[] = 'texts.technicalData';
		}
		$itemDescriptionFields[] = 'texts.lang';

		// Mutators

		/**
		 * @var DefaultCategoryMutator $defaultCategoryMutator
		 */
		$defaultCategoryMutator = pluginApp(DefaultCategoryMutator::class);

		if($defaultCategoryMutator instanceof DefaultCategoryMutator)
		{
			$defaultCategoryMutator->setPlentyId($settings->get('plentyId'));
		}

		/**
		 * @var ImageMutator $imageMutator
		 */
		$imageMutator = pluginApp(ImageMutator::class);
		if($imageMutator instanceof ImageMutator)
		{
			$imageMutator->addMarket($reference);
		}

		/**
		 * @var LanguageMutator $languageMutator
		 */
		$languageMutator = pluginApp(LanguageMutator::class, [[$settings->get('lang')]]);

		/**
		 * @var KeyMutator $keyMutator
		 */
		$keyMutator = pluginApp(KeyMutator::class);

		if($keyMutator instanceof KeyMutator)
		{
			$keyMutator->setKeyList($this->getKeyList());
			$keyMutator->setNestedKeyList($this->getNestedKeyList());
		}

		// Fields
		$fields = [
			[
				//item
				'item.id',
				'item.manufacturer.id',

				//variation
				'id',
				'variation.availability.id',

				//images
				'images.all.urlMiddle',
				'images.all.urlPreview',
				'images.all.urlSecondPreview',
				'images.all.url',
				'images.all.path',
				'images.all.position',

				'images.item.urlMiddle',
				'images.item.urlPreview',
				'images.item.urlSecondPreview',
				'images.item.url',
				'images.item.path',
				'images.item.position',

				'images.variation.urlMiddle',
				'images.variation.urlPreview',
				'images.variation.urlSecondPreview',
				'images.variation.url',
				'images.variation.path',
				'images.variation.position',

				//unit
				'unit.id',
				'unit.content',

				//defaultCategories
				'defaultCategories.id',

				//attributes
				'attributes.attributeId',
				'attributes.valueId',
				'attributes.attributeValueSetId',

				//properties
				'properties.property.id',
				'properties.property.names',
				'properties.property.valueType',
				'properties.selection.name',
				'properties.selection.lang',
				'properties.texts.value',
				'properties.texts.lang',
			],
			[
				$languageMutator,
				$defaultCategoryMutator,
				$keyMutator
			],
		];

		// Get the associated images if reference is selected
		if($reference != -1)
		{
			$fields[1][] = $imageMutator;
		}

		foreach($itemDescriptionFields as $itemDescriptionField)
		{
			//texts
			$fields[0][] = $itemDescriptionField;
		}

		return $fields;
	}

	/**
	 * Returns the key list for Elastic Export.
	 *
	 * @return array
	 */
	private function getKeyList()
	{
		$keyList = [
			//item
			'item.id',
			'item.manufacturer.id',

			//variation
			'variation.availability.id',
			'variation.stockLimitation',

			//unit
			'unit.content',
			'unit.id',
		];

		return $keyList;
	}

	/**
	 * Returns the nested key list for Elastic Export.
	 *
	 * @return array
	 */
	private function getNestedKeyList()
	{
		$nestedKeyList['keys'] = [
			//images
			'images.all',
			'images.item',
			'images.variation',

			//texts
			'texts',

			//defaultCategories
			'defaultCategories',

			//barcodes
			'barcodes',

			//attributes
			'attributes',

			//properties
			'properties'
		];

		$nestedKeyList['nestedKeys'] = [
			'images.all' => [
				'urlMiddle',
				'urlPreview',
				'urlSecondPreview',
				'url',
				'path',
				'position',
			],

			'texts'  => [
				'urlPath',
				'name1',
				'name2',
				'name3',
				'shortDescription',
				'description',
				'technicalData',
				'lang'
			],

			'defaultCategories' => [
				'id'
			],

			'barcodes'  => [
				'code',
				'type',
			],

			'attributes'   => [
				'attributeValueSetId',
				'attributeId',
				'valueId',
				'names.name',
				'names.lang',
			],

			'properties'    => [
				'property.id',
			]
		];

		return $nestedKeyList;
	}
}