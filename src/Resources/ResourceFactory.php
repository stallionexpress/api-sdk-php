<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Exceptions\ResourceFactoryException;
use MyParcelCom\Sdk\MyParcelComApiInterface;
use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CustomsItemInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\Sdk\Resources\Proxy\FileProxy;
use MyParcelCom\Sdk\Resources\Proxy\FileStreamProxy;
use MyParcelCom\Sdk\Resources\Proxy\RegionProxy;
use MyParcelCom\Sdk\Resources\Proxy\ShopProxy;
use MyParcelCom\Sdk\Resources\Proxy\StatusProxy;
use MyParcelCom\Sdk\Utils\StringUtils;
use ReflectionParameter;

class ResourceFactory implements ResourceFactoryInterface, ResourceProxyInterface
{
    private $typeFactory = [
        ResourceInterface::TYPE_CARRIER       => Carrier::class,
        ResourceInterface::TYPE_CONTRACT      => Contract::class,
        ResourceInterface::TYPE_PUDO_LOCATION => PickUpDropOffLocation::class,
        ResourceInterface::TYPE_REGION        => Region::class,
        ResourceInterface::TYPE_SHOP          => Shop::class,
        ResourceInterface::TYPE_STATUS        => Status::class,

        AddressInterface::class               => Address::class,
        CarrierInterface::class               => Carrier::class,
        ContractInterface::class              => Contract::class,
        CustomsInterface::class               => Customs::class,
        OpeningHourInterface::class           => OpeningHour::class,
        PhysicalPropertiesInterface::class    => PhysicalProperties::class,
        PickUpDropOffLocationInterface::class => PickUpDropOffLocation::class,
        PositionInterface::class              => Position::class,
        RegionInterface::class                => Region::class,
        ShipmentInterface::class              => Shipment::class,
        ShopInterface::class                  => Shop::class,
        StatusInterface::class                => Status::class,
    ];

    /** @var MyParcelComApiInterface */
    protected $api;

    public function __construct()
    {
        $shipmentFactory = [$this, 'shipmentFactory'];
        $serviceFactory = [$this, 'serviceFactory'];
        $serviceGroupFactory = [$this, 'serviceGroupFactory'];
        $serviceOptionFactory = [$this, 'serviceOptionFactory'];
        $serviceInsuranceFactory = [$this, 'serviceInsuranceFactory'];
        $fileFactory = [$this, 'fileFactory'];
        $customsItemFactory = [$this, 'customsItemFactory'];

        $this->setFactoryForType(ResourceInterface::TYPE_SHIPMENT, $shipmentFactory);
        $this->setFactoryForType(ShipmentInterface::class, $shipmentFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE, $serviceFactory);
        $this->setFactoryForType(ServiceInterface::class, $serviceFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE_GROUP, $serviceGroupFactory);
        $this->setFactoryForType(ServiceGroupInterface::class, $serviceGroupFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE_OPTION, $serviceOptionFactory);
        $this->setFactoryForType(ServiceOptionInterface::class, $serviceOptionFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE_INSURANCE, $serviceInsuranceFactory);
        $this->setFactoryForType(ServiceInsuranceInterface::class, $serviceInsuranceFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_FILE, $fileFactory);
        $this->setFactoryForType(FileInterface::class, $fileFactory);

        $this->setFactoryForType(CustomsItemInterface::class, $customsItemFactory);
    }

    /**
     * Shipment factory method that creates proxies for all relationships.
     *
     * @param string $type
     * @param array  $attributes
     * @return Shipment
     */
    protected function shipmentFactory($type, array &$attributes)
    {
        $shipment = new Shipment();

        if (isset($attributes['files'])) {
            array_walk($attributes['files'], function ($file) use ($shipment) {
                $shipment->addFile(
                    (new FileProxy())->setMyParcelComApi($this->api)->setId($file['id'])
                );
            });

            unset($attributes['files']);
        }

        if (isset($attributes['shop']['id'])) {
            $shipment->setShop(
                (new ShopProxy())->setMyParcelComApi($this->api)->setId($attributes['shop']['id'])
            );

            unset($attributes['shop']);
        }

        if (isset($attributes['status']['id'])) {
            $shipment->setStatus(
                (new StatusProxy())->setMyParcelComApi($this->api)->setId($attributes['status']['id'])
            );

            unset($attributes['status']);
        }

        if (isset($attributes['price']['amount'])) {
            $shipment->setPrice($attributes['price']['amount']);
            $shipment->setCurrency($attributes['price']['currency']);

            unset($attributes['price']);
        }

        if (isset($attributes['insurance']['amount'])) {
            $shipment->setInsuranceAmount($attributes['insurance']['amount']);
            if (!$shipment->getCurrency()) {
                $shipment->setCurrency($attributes['insurance']['currency']);
            }

            unset($attributes['insurance']);
        }

        return $shipment;
    }

    /**
     * Service factory method that creates proxies for all relationships.
     *
     * @param string $type
     * @param array  $attributes
     * @return Service
     */
    protected function serviceFactory($type, array &$attributes)
    {
        $service = new Service();

        if (isset($attributes['region_from']['id'])) {
            $service->setRegionFrom(
                (new RegionProxy())->setMyParcelComApi($this->api)->setId($attributes['region_from']['id'])
            );

            unset($attributes['region_from']);
        }
        if (isset($attributes['region_to']['id'])) {
            $service->setRegionTo(
                (new RegionProxy())->setMyParcelComApi($this->api)->setId($attributes['region_to']['id'])
            );

            unset($attributes['region_to']);
        }

        if (isset($attributes['links']['contracts'])) {
            $service->setContracts($this->api->getResourcesFromUri($attributes['links']['contracts']));

            unset($attributes['links']['contracts']);
        }

        return $service;
    }

    /**
     * ServiceGroup factory method.
     *
     * @param string $type
     * @param array  $attributes
     * @return ServiceGroup
     */
    protected function serviceGroupFactory($type, &$attributes)
    {
        $serviceGroup = new ServiceGroup();

        if (!isset($attributes['attributes'])) {
            return $serviceGroup;
        }

        if (isset($attributes['attributes']['price']['amount'])) {
            $attributes += [
                'price'    => $attributes['attributes']['price']['amount'],
                'currency' => $attributes['attributes']['price']['currency'],
            ];
        }
        if (isset($attributes['attributes']['weight']['min'])) {
            $attributes += [
                'weight_min' => $attributes['attributes']['weight']['min'],
                'weight_max' => $attributes['attributes']['weight']['max'],
            ];
        }
        if (isset($attributes['attributes']['step_price']['amount'])) {
            $attributes += [
                'step_price' => $attributes['attributes']['step_price']['amount'],
                'currency'   => $attributes['attributes']['step_price']['currency'],
            ];
        }
        if (isset($attributes['attributes']['step_size'])) {
            $attributes += [
                'step_size' => $attributes['attributes']['step_size'],
            ];
        }

        unset($attributes['attributes']);

        return $serviceGroup;
    }

    /**
     * ServiceOption factory method.
     *
     * @param string $type
     * @param array  $attributes
     * @return ServiceOption
     */
    protected function serviceOptionFactory($type, &$attributes)
    {
        $serviceOption = new ServiceOption();

        if (isset($attributes['attributes']['price']['amount'])) {
            $serviceOption->setPrice($attributes['attributes']['price']['amount']);
            $serviceOption->setCurrency($attributes['attributes']['price']['currency']);

            unset($attributes['attributes']['price']);
        }

        if (isset($attributes['attributes'])) {
            $attributes += $attributes['attributes'];

            unset($attributes['attributes']);
        }

        return $serviceOption;
    }

    /**
     * ServiceInsurance factory method.
     *
     * @param string $type
     * @param array  $attributes
     * @return ServiceInsurance
     */
    protected function serviceInsuranceFactory($type, &$attributes)
    {
        $serviceInsurance = new ServiceInsurance();

        if (isset($attributes['attributes']['price']['amount'])) {
            $serviceInsurance->setPrice($attributes['attributes']['price']['amount']);
            $serviceInsurance->setCurrency($attributes['attributes']['price']['currency']);

            unset($attributes['attributes']['price']);
        }

        if (isset($attributes['attributes']['covered']['amount'])) {
            $serviceInsurance->setCovered($attributes['attributes']['covered']['amount']);
            if (!$serviceInsurance->getCurrency()) {
                $serviceInsurance->setCurrency($attributes['attributes']['covered']['currency']);
            }

            unset($attributes['attributes']['covered']);
        }

        if (isset($attributes['attributes'])) {
            $attributes += $attributes['attributes'];

            unset($attributes['attributes']);
        }

        return $serviceInsurance;
    }

    /**
     * Factory method for creating file resources, adds proxy streams to the
     * file for requesting the file data.
     *
     * @param $type
     * @param $attributes
     * @return File
     */
    protected function fileFactory($type, &$attributes)
    {
        $file = new File();

        if (!isset($attributes['formats'])) {
            return $file;
        }

        array_walk($attributes['formats'], function ($format) use ($file, $attributes) {
            $file->setStream(
                new FileStreamProxy($attributes['id'], $format['mime_type'], $this->api),
                $format['mime_type']
            );
        });

        return $file;
    }

    /**
     * Factory for creating a customs item.
     *
     * @param $type
     * @param $attributes
     * @return CustomsItem
     */
    protected function customsItemFactory($type, &$attributes)
    {
        $item = new CustomsItem();

        if (isset($attributes['item_value']['amount'])) {
            $item->setItemValue($attributes['item_value']['amount']);
            $item->setCurrency($attributes['item_value']['currency']);

            unset($attributes['item_value']);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, array $attributes = [])
    {
        return $this->hydrate(
            $this->createResource($type, $attributes),
            $attributes
        );
    }

    /**
     * Set a factory method or class string for given resource type.
     *
     * @param string          $type
     * @param callable|string $factory
     */
    public function setFactoryForType($type, $factory)
    {
        if (!is_callable($factory) && !class_exists($factory)) {
            throw new ResourceFactoryException(sprintf(
                'Cannot assign factory for type `%s`, given factory was not a valid callable or class',
                $type
            ));
        }

        $this->typeFactory[$type] = $factory;
    }

    /**
     * Checks if the given type has a factory associated with it.
     *
     * @param string $type
     * @return bool
     */
    protected function typeHasFactory($type)
    {
        return array_key_exists($type, $this->typeFactory);
    }

    /**
     * Create a resource for type using its factory or the class associated with it.
     *
     * @param string $type
     * @param array  $attributes
     * @throws ResourceFactoryException
     * @return object
     */
    protected function createResource($type, array &$attributes = [])
    {
        if (!$this->typeHasFactory($type)) {
            throw new ResourceFactoryException(sprintf(
                'Could not create resource of type `%s`, no class or factory specified',
                $type
            ));
        }

        $factory = $this->typeFactory[$type];

        if (is_callable($factory)) {
            return $factory($type, $attributes);
        } elseif (class_exists($factory)) {
            return new $factory();
        }

        throw new ResourceFactoryException(sprintf(
            'Could not determine how to create a resource of type `%s`, no factory method or class defined',
            $type
        ));
    }

    /**
     * Hydrates resource with given attributes. Uses reflection do determine if
     * other resources need to be created and tries to instantiate them where
     * possible.
     *
     * @todo Refactor this huge moth.
     *
     * @param object $resource
     * @param array  $attributes
     * @return object
     */
    protected function hydrate($resource, array $attributes)
    {
        array_walk($attributes, function ($value, $key) use ($resource) {
            $setter = 'set' . StringUtils::snakeToPascalCase($key);

            // Can't use setter if it doesn't exist.
            if (!method_exists($resource, $setter)) {
                return;
            }

            $param = $this->getFillableParam($resource, $setter);
            // Can't use the setter if we cannot determine the param to fill.
            if ($param === null) {
                return;
            }

            if ($param->isArray()) {
                // Can't use setter if the types don't match.
                if (!is_array($value)) {
                    return;
                }

                $adder = trim('add' . StringUtils::snakeToPascalCase($key), 's');

                if (($adderParam = $this->getFillableParam($resource, $adder)) !== null) {
                    $adderParamClass = $adderParam->getClass();


                    if ($adderParamClass !== null) {
                        $className = $adderParamClass->getName();

                        foreach ($value as $entry) {
                            if (is_array($entry) && $this->typeHasFactory($className)) {
                                $resource->$adder($this->create($className, $entry));

                                continue;
                            }
                            if ($entry instanceof $className) {
                                $resource->$adder($entry);

                                continue;
                            }
                        }

                        return;
                    }
                }
            }

            $paramClass = $param->getClass();
            if ($paramClass !== null) {
                $className = $paramClass->getName();

                if (is_array($value) && $this->typeHasFactory($className)) {
                    $resource->$setter($this->create($className, $value));

                    return;
                }

                if (!$value instanceof $className) {
                    return;
                }
            }

            $resource->$setter($value);
        });

        return $resource;
    }

    /**
     * @param mixed  $resource
     * @param string $method
     * @return ReflectionParameter|null
     */
    private function getFillableParam($resource, $method)
    {
        $params = (new \ReflectionMethod($resource, $method))
            ->getParameters();


        // Can't use setter if it requires no params.
        if (count($params) === 0) {
            return null;
        }

        // Check if all parameters after the 1st are optional. If not, we
        // cannot use the setter.
        foreach (array_slice($params, 1) as $param) {
            if (!$param->isOptional()) {
                return null;
            }
        }

        return reset($params);
    }

    /**
     * @param MyParcelComApiInterface $api
     * @return $this
     */
    public function setMyParcelComApi(MyParcelComApiInterface $api)
    {
        $this->api = $api;

        return $this;
    }
}
