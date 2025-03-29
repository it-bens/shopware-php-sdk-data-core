# Shopware 6 PHP SDK data core

This package contains the core data classes, interfaces and traits for the [Shopware 6 PHP SDK](https://github.com/it-bens/shopware-php-sdk).

## `Struct`

The abstract base class for all structured data in the SDK. It implements the usage of extensions and provides a method so recursively normalize the data.

## `Collection`

The abstract base class for all collections in the SDK. A `Collection` is itself a `Struct` implementation. I can hold any object type. It is implemented as an invariant generic. The `Countable` and `IteratorAggregate` interfaces are implemented.

## `Entity`

The abstract base class for all entities in the SDK. An entity is a `Struct` with an identifier that provides a type-safe and relation-supporting property handling. All entities share two additional properties: custom fields and translations.

## `EntityCollection`

The abstract base class for all entity collections in the SDK. An `EntityCollection` is `Collection` implementation that provides methods to insert entities, merge collections, and filter entities by property values.

## `EntityDefinition`

The interface that defines how an `Entity` is linked to its name, its `EntityCollection` and its `Schema`.

## `Schema` and `Property`

A `Schema` contains a collection of `Property` objects. A `Property` is a data structure that describes what information is provided by Shopware for this entity.