<?php

namespace ElasticExportFashionDE\Helper;


use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Helper\Models\KeyValue;

class PropertyHelper
{
	/**
	 * @var PropertyNameRepositoryContract
	 */
	private $propertyNameRepository;

	/**
	 * PropertyHelper constructor.
	 * @param PropertyNameRepositoryContract $
	 */
	public function __construct(PropertyNameRepositoryContract $propertyNameRepository)
	{
		$this->propertyNameRepository = $propertyNameRepository;
	}

	/**
	 * @param array $variation
	 * @param KeyValue $settings
	 * @param String $backendName
	 * @return string
	 */
	public function getPropertyValueByBackendName($variation, $backendName)
	{
		foreach($variation['data']['properties'] as $property)
		{
			if($property['property']['names']['name'] == $backendName)
			{
				switch($property['property']['valueType'])
				{
					case "selection":
						return $property['selection']['name'];
					case "text":
						return $property['texts']['value'];
					default:
						return '';
				}
			}
		}
		return '';
	}
}