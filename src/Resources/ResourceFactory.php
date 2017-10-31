<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Exceptions\ResourceFactoryException;
use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\Sdk\Resources\Interfaces\Position;
use MyParcelCom\Sdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Utils\StringUtils;
use ReflectionParameter;

class ResourceFactory implements ResourceFactoryInterface
{
    private $typeFactory = [
        ResourceInterface::TYPE_CARRIER           => Carrier::class,
        ResourceInterface::TYPE_CONTRACT          => Contract::class,
        ResourceInterface::TYPE_FILE              => File::class,
        ResourceInterface::TYPE_PUDO_LOCATION     => PickUpDropOffLocation::class,
        ResourceInterface::TYPE_REGION            => Region::class,
        ResourceInterface::TYPE_SHIPMENT          => Shipment::class,
        ResourceInterface::TYPE_SHOP              => Shop::class,
        ResourceInterface::TYPE_SERVICE           => Service::class,
        ResourceInterface::TYPE_SERVICE_GROUP     => ServiceGroup::class,
        ResourceInterface::TYPE_SERVICE_OPTION    => ServiceOption::class,
        ResourceInterface::TYPE_SERVICE_INSURANCE => ServiceInsurance::class,

        AddressInterface::class               => Address::class,
        CarrierInterface::class               => Carrier::class,
        ContractInterface::class              => Contract::class,
        FileInterface::class                  => File::class,
        OpeningHourInterface::class           => OpeningHour::class,
        PhysicalPropertiesInterface::class    => PhysicalProperties::class,
        PickUpDropOffLocationInterface::class => PickUpDropOffLocation::class,
        PositionInterface::class              => Position::class,
        RegionInterface::class                => Region::class,
        ShipmentInterface::class              => Shipment::class,
        ShopInterface::class                  => Shop::class,
        ServiceInterface::class               => Service::class,
        ServiceGroupInterface::class          => ServiceGroup::class,
        ServiceOptionInterface::class         => ServiceOption::class,
        ServiceInsuranceInterface::class      => ServiceInsurance::class,
    ];

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
    protected function createResource($type, array $attributes = [])
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
}
