Doctrine Tools for Symfony
====

![Build Status](https://github.com/headsnet/doctrine-tools-bundle/actions/workflows/ci.yml/badge.svg)
![Coverage](https://raw.githubusercontent.com/headsnet/doctrine-tools-bundle/image-data/coverage.svg)
[![Latest Stable Version](https://poser.pugx.org/headsnet/doctrine-tools-bundle/v)](//packagist.org/packages/headsnet/doctrine-tools-bundle)
[![Total Downloads](https://poser.pugx.org/headsnet/doctrine-tools-bundle/downloads)](//packagist.org/packages/headsnet/doctrine-tools-bundle)
[![License](https://poser.pugx.org/headsnet/doctrine-tools-bundle/license)](//packagist.org/packages/headsnet/doctrine-tools-bundle)
[![PHP Version Require](http://poser.pugx.org/headsnet/doctrine-tools-bundle/require/php)](//packagist.org/packages/headsnet/doctrine-tools-bundle)

Various tools and helpers for using Doctrine ORM in larger Symfony projects.

## Installation

```bash
composer require headsnet/doctrine-tools
```
If your Symfony installation does not auto-register bundles, add it manually:

```php
// bundles.php
return [
    ...
    Headsnet\DoctrineToolsBundle\HeadsnetDoctrineToolsBundle::class => ['all' => true],
];
```

## Features

- [Dynamically Create Types For Standard Objects](#dynamically-create-types-for-standard-objects)
- [Auto-Register Custom Doctrine Type Mappings](#auto-register-custom-doctrine-type-mappings)
- [Auto-Register Carbon Doctrine Type Mappings](#auto-register-carbon-datetime-type-mappings)

### Dynamically Create Types For Standard Objects

Applying the `#[DoctrineType]` attribute to a class will generate a Doctrine mapping type for the object and register 
it in the application. To configure this behaviour, you must specify where to look for the classes to auto-register.

```yaml
headsnet_doctrine_tools:
  root_namespace: Headsnet\DoctrineToolsBundle
  custom_types:
    scan_dirs:
      - 'src/' # the default value
```

Currently the bundle supports the following standard types:

- `string` via `AbstractStringMappingType`
- `integer` via `AbstractIntegerMappingType`
- `uuid` via `AbstractUuidMappingType`

For example, we have a string backed value object:

```php
namespace App\Domain\Model;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;

#[DoctrineType(name: 'person_name', type: 'string')]
class PersonName
{
    public function __construct(
        private readonly string $value
    ) {
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function asString(): string
    {
        return $this->value;
    }
}
```

This will cause a PHP file to be generated that provides the Doctrine Mapping Type for the class - something like:

```php
class PersonNameType extends \Headsnet\DoctrineToolsBundle\Types\StandardTypes\AbstractStringMappingType {
    public function getName(): string
    {
        return 'person_name';
    }

    public function getClass(): string
    {
        return 'App\Domain\Model\PersonName';
    }
}
```

This file is created in `src/_generated/` - you will want to add this directory to your `.gitignore` file.

### Auto-Register Custom Doctrine Type Mappings

The bundle can auto-register custom Doctrine DBAL types, eliminating the need to specify them all in 
`config/packages/doctrine.yaml`:

Define the directories to scan for Doctrine types:

```yaml
headsnet_doctrine_tools:
  custom_mappings:
    scan_dirs:
      - 'src/Infra/Persistence/DBAL/Types'
```

Then add the `#[DoctrineTypeMapping]` attribute to the custom type class:

```php
use Doctrine\DBAL\Types\Type;
use Headsnet\DoctrineToolsBundle\Attribute\DoctrineMappingType;

#[DoctrineTypeMapping]
final class ReservationIdType extends Type
{
    // defines "reservation_id" type
}
```

This will register a custom type based on the class name - in this case the custom column type will be called 
`reservation_id`.

To customise the type name, specify it in the `#[DoctrineTypeMapping]` attribute. The following will register a type 
called `my_reservation_id`.

```php
#[DoctrineTypeMapping(name: 'my_reservation_id')]
final class ReservationIdType extends Type
{
    // customised name "my_reservation_id" type
}
```

### Auto-Register Carbon datetime type mappings

If the `nesbot/carbon` package is installed, this package will automatically register the Doctrine types provided by 
Carbon.

By default, it will overwrite the default Doctrine types for `datetime` and `datetime_immutable` with the Carbon 
equivalents:

```yaml
datetime_immutable: \Carbon\Doctrine\DateTimeImmutableType
datetime: \Carbon\Doctrine\DateTimeType
```
If you wish the Carbon types to operate alongside the default DateTime and DateTimeImmutable types, set `replace: 
false` in the bundle configuration. This will result in additional types being defined for the Carbon columns.

```yaml
carbon_immutable: \Carbon\Doctrine\DateTimeImmutableType
carbon: \Carbon\Doctrine\DateTimeType
```

If you wish to completely disable this behaviour, set `enabled: false` in the bundle configuration.

```yaml
headsnet_doctrine_tools:
  carbon_mappings:
    enabled: true
    replace: true
```

## Contributions

Contributions are welcome via Pull Requests. Please include a single change in each PR.

## License

Released under the [MIT License](LICENSE).
