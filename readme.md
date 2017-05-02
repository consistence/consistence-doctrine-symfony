Symfony Bundle integrating Consistence library with Doctrine ORM
================================================================

> This is a Symfony bundle providing integration for the standalone package
[`consistence/consistence-doctrine`](https://github.com/consistence/consistence-doctrine),
if you are not using Symfony, follow instructions there.

This bundle provides integration of [Consistence](https://github.com/consistence/consistence) value objects for [Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/) so that you can use them in your entities.

For now, the only integration which is needed is for [Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md), see the examples below.

Usage
-----

[Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md) represent predefined set of values and of course, you will want to store these values in your database as well. Since [`Enums`](https://github.com/consistence/consistence/blob/master/src/Enum/Enum.php) are objects and you only want to store the represented value, there has to be some mapping.

You can see it in this example where you want to store sex for your `User`s:

```php
<?php

namespace Consistence\Doctrine\Example\User;

class Sex extends \Consistence\Enum\Enum
{

	const FEMALE = 'female';
	const MALE = 'male';

}
```

Now you can use the `Sex` enum in your `User` entity. There are two important things to notice:

1) `type="string_enum"` in `ORM\Column` - this will be used for mapping the value to your database, that means if you have a string based enum (see values in `Sex`), use `string_enum`

You can specify any other parameters for `ORM\Column` as you would usually (nullability, length...).

There is also `integer_enum` and `float_enum` which can be used respectively for their types.

2) `@Enum(class=Sex::class)` - this will be used for reconstructing the `Sex`
 enum object when loading the value back from database

The `class` annotation parameter uses the same namespace resolution process as other Doctrine annotations, so it is practically the same as when you specify a `targetEntity` in associations mapping.

```php
<?php

namespace Consistence\Doctrine\Example\User;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends \Consistence\ObjectPrototype
{

	// ...

	/**
	 * @Enum(class=Sex::class)
	 * @ORM\Column(type="string_enum", nullable=true)
	 * @var \Consistence\Doctrine\Example\User\Sex|null
	 */
	private $sex;

	// ...

	public function __construct(
		// ...
		Sex $sex = null
		// ...
	)
	{
		// ...
		$this->sex = $sex;
		// ...
	}

	// ...

}
```

Now everything is ready to be used, when you call `flush`, only `female` will be saved:

```php
<?php

namespace Consistence\Doctrine\Example\User;

$user = new User(
	// ...
	Sex::get(Sex::FEMALE)
	// ...
);
/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager->persist($user);

// when persisting User::$sex to database, `female` will be saved
$entityManager->flush();
```

And when you retrieve the entity back from database, you will receive the `Sex` enum object again:

```php
<?php

namespace Consistence\Doctrine\Example\User;

/** @var \Doctrine\ORM\EntityManager $entityManager */
$user = $entityManager->find(User::class, 1);
var_dump($user->getSex());

/*

class Consistence\Doctrine\Example\User\Sex#5740 (1) {
  private $value =>
  string(6) "female"
}

*/
```

This means that the objects API is symmetrical (you get the same type as you set) and you can start benefiting from [Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md) advantages such as being sure, that what you get is already a valid value and having the possibility to define methods on top of the represented values.

Installation
------------

1) Install package [`consistence/consistence-doctrine-symfony`](https://packagist.org/packages/consistence/consistence-doctrine-symfony) with [Composer](https://getcomposer.org/):

```bash
composer require consistence/consistence-doctrine-symfony
```

2) Register the bundle in your application kernel:

```php
// app/AppKernel.php
public function registerBundles()
{
	return [
		// ...
		new Consistence\Doctrine\SymfonyBundle\ConsistenceDoctrineBundle(),
	];
}
```

That's all, you are good to go!
