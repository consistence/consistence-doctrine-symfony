<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle;

use Consistence\Doctrine\Enum\Type\BooleanEnumType;
use Consistence\Doctrine\Enum\Type\FloatEnumType;
use Consistence\Doctrine\Enum\Type\IntegerEnumType;
use Consistence\Doctrine\Enum\Type\StringEnumType;
use Doctrine\DBAL\Types\Type as DbalType;
use Generator;
use PHPUnit\Framework\Assert;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsistenceDoctrineBundleTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function registerDbalTypesDataProvider(): Generator
	{
		yield 'empty configuration' => [
			'configuration' => [],
			'expectedDbalTypes' => [],
		];

		yield 'empty enum configuration' => [
			'configuration' => [
				'enum' => [],
			],
			'expectedDbalTypes' => [],
		];

		yield 'empty enum.dbal_types configuration' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [],
				],
			],
			'expectedDbalTypes' => [],
		];

		yield 'register only one type' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'integer' => [
							FooIntegerEnum::class,
						],
					],
				],
			],
			'expectedDbalTypes' => [
				'enum<Consistence\Doctrine\SymfonyBundle\FooIntegerEnum>' => IntegerEnumType::class,
			],
		];

		yield 'register all types' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'boolean' => [
							FooBooleanEnum::class,
						],
						'float' => [
							FooFloatEnum::class,
						],
						'integer' => [
							FooIntegerEnum::class,
						],
						'string' => [
							FooStringEnum::class,
						],
					],
				],
			],
			'expectedDbalTypes' => [
				'enum<Consistence\Doctrine\SymfonyBundle\FooBooleanEnum>' => BooleanEnumType::class,
				'enum<Consistence\Doctrine\SymfonyBundle\FooFloatEnum>' => FloatEnumType::class,
				'enum<Consistence\Doctrine\SymfonyBundle\FooIntegerEnum>' => IntegerEnumType::class,
				'enum<Consistence\Doctrine\SymfonyBundle\FooStringEnum>' => StringEnumType::class,
			],
		];
	}

	/**
	 * @dataProvider registerDbalTypesDataProvider
	 *
	 * @param mixed[] $configuration
	 * @param string[] $expectedDbalTypes
	 */
	public function testRegisterDbalTypes(
		array $configuration,
		array $expectedDbalTypes
	): void
	{
		$bundle = self::createBundle($configuration);
		$bundle->boot();

		$registeredTypesMap = DbalType::getTypesMap();
		foreach ($expectedDbalTypes as $name => $class) {
			Assert::assertArrayHasKey($name, $registeredTypesMap);
			Assert::assertSame($class, $registeredTypesMap[$name]);
		}
		if (count($expectedDbalTypes) === 0) {
			$this->expectNotToPerformAssertions();
		}
	}

	/**
	 * @param mixed[] $configuration
	 * @return \Consistence\Doctrine\SymfonyBundle\ConsistenceDoctrineBundle
	 */
	private static function createBundle(array $configuration): ConsistenceDoctrineBundle
	{
		$configuration = [
			'consistence_doctrine' => $configuration,
		];

		$container = new ContainerBuilder();
		$bundle = new ConsistenceDoctrineBundle();
		$extension = $bundle->getContainerExtension();
		$container->registerExtension($extension);
		$bundle->build($container);

		$extension->load($configuration, $container);

		$container->compile();

		$bundle->setContainer($container);

		return $bundle;
	}

}
