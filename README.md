# MyParcel.com SDK

The MyParcel.com SDK is a PHP library that can be used to easily communicate
with the MyParcel.com API. For more information about the MyParcel.com API, see
the [MyParcel.com Docs](https://docs.myparcel.com/).
 
As the MyParcel.com API is updated with new features,
these features will be added to the SDK for easier access. Currently the
following features are supported by the SDK:

- Authentication with the MyParcel.com OAuth2.0 server, using the
  [`client_credentials`](https://tools.ietf.org/html/rfc6749#section-4.4) grant.
- Shipment creation.
- Resource retrieval of types: `shops`, `regions`, `shipments`, `files`,
  `carriers` and `pickup-dropoff-locations`.

## Installation

The easiest way to install the sdk and keep it up to date, is to use composer.

### Composer

[Install PHP Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require myparcelcom/api-sdk
```

## Usage

To start communicating with the MyParcel.com API, you need to create an instance
of the `MyParcelComApi` class. This class will facilitate all interaction with
the API. The URL to the MyParcel.com API should be supplied as the first
argument during construction. If no url is supplied it will default to the 
sandbox URL.

```php
$api = new \MyParcelCom\ApiSdk\MyParcelComApi(
    'https://sandbox-api.myparcel.com'
);
```

For convenience, a singleton of the `MyParcelComApi` class can be instantiated
using its static methods.

```php
<?php
// Create the singleton once, to make it available everywhere.
$api = \MyParcelCom\ApiSdk\MyParcelComApi::createSingleton(
    new \MyParcelCom\ApiSdk\Authentication\ClientCredentials(
        'client-id',
        'client-secret',
        'https://sandbox-auth.myparcel.com'
    ),
    'https://sandbox-api.myparcel.com'
);

// The singleton instance can now be retrieved anywhere.
$api = \MyParcelCom\ApiSdk\MyParcelComApi::getSingleton();
```

### Authentication

Most interactions with the MyParcel.com API will require authentication. A class
for authentication using the `client_credentials` can be used to authenticate
the user. A `client id` and `client secret` are needed to authenticate with the
OAuth2.0 server. A URL should be supplied to define te location of the  OAuth2.0
server.

```php
$authenticator = new \MyParcelCom\ApiSdk\Authentication\ClientCredentials(
    'client-id',
    'client-secret', 
    'https://sandbox-auth.myparcel.com'
);

$api->authenticate($authenticator);
```

### Resources

Most of the resources available in the MyParcel.com API can be accessed using
the SDK. All resources will be mapped to classes implementing their specific
interface. These interfaces are all defined in the
`\MyParcelCom\ApiSdk\Resources\Interfaces` namespace.

#### Shops

All the shops or the default shop for the currently authenticated user can be
retrieved. The shops will be mapped to objects implementing
`\MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface`.

```php
// Get all shops.
$shops = $api->getShops();

// Get the default shop.
$shop = $api->getDefaultShop();
```
#### Shipments

Shipments are the resources that you will interact with the most. Creating and
retrieving shipments can be done through the MyParcel.com SDK. As wel as
retrieving the shipment status and any files associated with the shipment.

To create a shipment an object implementing
`\MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface` should be created. A
class implementing this interface has been provided in
`\MyParcelCom\ApiSdk\Resources\Shipment`. At least a recipient address and a weight
should be provided in the shipment. All other fields are optional or will be
filled with defaults by the SDK.

```php
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Shipment;

// Define the recipient address.
$recipient = new Address();
$recipient->setStreet1('Street name')
          ->setStreetNumber(9)
          ->setCity('City name')
          ->setPostalCode('Postal code')
          ->setFirstName('First name')
          ->setLastName('Last name')
          ->setCountryCode('GB')
          ->setEmail('email@example.com');

// Define the weight.
$shipment = new Shipment();
$shipment->setRecipientAddress($recipient)
         ->setWeight(500, Shipment::WEIGHT_GRAM);

// Create the shipment
$api->createShipment($shipment);
```

If the shipment being created is invalid or there is no valid service available,
an exception will be thrown.

If you wish to use your own contracts with your shipment, you should assign it
to the shipment before creating it.

After the shipment has been created, it will be updated with an id and a price.
Using the id the shipment can be retrieved from the MyParcel.com API to check
the status and retrieve any associated files.

```php
// Get the shipment based on its id. 
$shipment = $api->getShipment('shipment-id');

// Get the current status of the shipment.
$status = $shipment->getStatus();

// Get the files associated with the shipment, eg label.
$files = $shipment->getFiles();
```

#### Files

When a shipment has been successfully registered with a carrier, a shipping
label will be available for the shipment. In some cases the shipping label is
accompanied by one of more additional files. (eg when creating a PostNL
shipment we also return a print code, that can be used to print a label on a
pick-up/drop-off location in case customer has no printer). These files can be
requested from a shipment.

#### Carriers

Services for different carriers are available through the MyParcel.com API. The
SDK can retrieve all the carriers the currently authenticated user can access.
All carriers will be mapped to objects implementing
`MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface`.

```php
// Get the carriers.
$carriers = $api->getCarriers();
```

#### Services

The services available in the MyParcel.com API can be retrieved using the SDK.
All available services can be retrieved, services available for a specific 
shipment can be retrieved and services for a specific carrier can be retrieved.

```php
// Get all services.
$services = $api->getServices();

// Get all services that can handle the shipment.
$services = $api->getServices($shipment);

// Get all services for specific carrier.
$services = $api->getServicesForCarrier($carrier);
```

#### Contracts

Each service has contracts associated with it. A contract determines the price
for the shipment and what options are available (eg 'sign on delivery'). These
contracts can be retrieved from a service.

```php
// Get the contracts for this service.
$contracts = $service->getContracts();
$contract = $contracts[0];

// Get the weight groups for this contract and the prices.
$contract->getGroups();

// Get the insurance groups for this contract and the prices.
$contract->getInsurances();

// Get the options for this contract (eg 'sign on delivery').
$contract->getOptions();
```

When creating a shipment either a specific contract can be selected, or the
MyParcel.com SDK will select a preferred contract.

#### Pick-up drop-off locations

Most carriers allow the recipient to define a pick-up location and a sender to
define a drop-off location. The MyParcel.com SDK can retrieve these locations
from the API and can be easily displayed using the
[MyParcel.com Delivery Plugin](https://github.com/MyParcelCOM/delivery-plugin).

Most carriers only need a postal code in a specific country, but some
carriers also require a street name and number. It is therefore recommended to
always supply all this information to the SDK.

```php
// Get all pick-up/drop-off locations near the area with postal code '1AR BR2'
// in the United Kingdom for all carriers.  
$locations = $api->getPickUpDropOffLocations('GB', '1AR BR2', 'Street name', 4);

// Same as above, but for specified carrier.
$locations = $api->getPickUpDropOffLocations('GB', '1AR BR2', 'Street name', 4, $carrier);
```

#### Regions

The MyParcel.com API supports sending parcels from one country/state/province
to another. These are split up into `regions` in the MyParcel.com API. These
are mostly used to define which services are available between what regions. A
list of these regions as defined by the API can be retrieved through the SDK.

```php
// Get all the regions.
$api->getRegions();

// Get all the regions in the United Kingdom.
$api->getRegions('GB');

// Get the region for Scotland.
$api->getRegions('GB', 'SCH');
```

### File Combining

The MyParcel.com SDK provides a class for combining files into 1 pdf. Using this
you can create a pdf file with multiple labels for printing. The class takes an
array of objects that implement `FileInterface` and returns a new object that
implements `FileInterface`.

```php
use MyParcelCom\ApiSdk\LabelCombiner;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;

$files = array_merge(
    $shipmentA->getFiles(FileInterface::RESOURCE_TYPE_LABEL),
    $shipmentB->getFiles(FileInterface::RESOURCE_TYPE_LABEL)
);

$labelCombiner = new LabelCombiner();
$combinedFile = $labelCombiner->combineLabels($files);
```

The page size (A4, A5, A6), the starting position as well as a margin can be
specified when combining the labels.

```php
use MyParcelCom\ApiSdk\LabelCombinerInterface;

$combinedFile = $labelCombiner->combineLabels(
    $files,
    LabelCombinerInterface::PAGE_SIZE_A4,
    LabelCombinerInterface::LOCATION_BOTTOM_LEFT,
    20
);
```

## Advanced usage

### Caching

By default the MyParcel.com SDK uses the filesystem to cache both resources and
access tokens. To use another type of caching, any cache instance implementing 
`Psr\SimpleCache\CacheInterface` can be used. This instance should be supplied
at construction of `MyParcelComApi` and `ClientCredentials`.

```php
$redis = new RedisCache();
$api = new \MyParcelCom\ApiSdk\MyParcelComApi(
    'https://sandbox-api.myparcel.com',
    $redis
);

$authenticator = new \MyParcelCom\ApiSdk\Authentication\ClientCredentials(
    'client-id',
    'client-secret', 
    'https://sandbox-auth.myparcel.com',
    $redis
);
```

### Configuring a different http client

The MyParcel.com SDK uses Guzzle to send http to the MyParcel.com API. If the
Guzzle Client needs to be configured differently for your setup (eg. you need to
connect through a proxy), then you can supply the SDK with a different client.

```php
// Create a Guzzle client that connects through a proxy.
$client = new \GuzzleHttp\Client([  
    'proxy' => [
        'http'  => 'tcp://localhost:8125',
        'https' => 'tcp://localhost:9124',
    ],
]);

// Add the client to the authenticator and api.
$authenticator->setHttpClient($client);
$api->setHttpClient($client);
``` 

### Custom resource classes

The MyParcel.com SDK uses the `MyParcelCom\ApiSdk\Resources\ResourceFactory` to
instantiate and hydrate all resource objects. If you want the SDK to instantiate
your own classes and hydrate them, a `ResourceFactory` can be created and factory
callables can be added to it to define how to instantiate a resource. Note that
when using your custom classes, they should still implement the corresponding
resource's interface.

```php
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\ResourceFactory;

class CustomShipment implements ShipmentInterface
{
    // Your shipment implementation.
}

$customShipmentInitializer = function ($type, $attributes) {
    $shipment = new CustomShipment();
    
    // Your shipment initialization.
    
    return $shipment;
};

$factory = new ResourceFactory();
$factory->setFactoryForType(ResourceInterface::TYPE_SHIPMENT, $customShipmentInitializer);
$factory->setFactoryForType(ShipmentInterface::class, $customShipmentInitializer);

$api->setResourceFactory($factory);
```
