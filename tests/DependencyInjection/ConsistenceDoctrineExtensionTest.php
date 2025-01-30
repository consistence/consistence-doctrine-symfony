<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Consistence\Doctrine\SymfonyBundle\FooBooleanEnum;
use Consistence\Doctrine\SymfonyBundle\FooFloatEnum;
use Consistence\Doctrine\SymfonyBundle\FooIntegerEnum;
use Consistence\Doctrine\SymfonyBundle\FooStringEnum;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsistenceDoctrineExtensionTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function registerDbalTypesDataProvider(): Generator
	{
		yield 'empty configuration' => [
			'configuration' => [],
			'expectedBooleanEnumClasses' => [],
			'expectedFloatEnumClasses' => [],
			'expectedIntegerEnumClasses' => [],
			'expectedStringEnumClasses' => [],
		];

		yield 'empty enum configuration' => [
			'configuration' => [
				'enum' => [],
			],
			'expectedBooleanEnumClasses' => [],
			'expectedFloatEnumClasses' => [],
			'expectedIntegerEnumClasses' => [],
			'expectedStringEnumClasses' => [],
		];

		yield 'empty enum.dbal_types configuration' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [],
				],
			],
			'expectedBooleanEnumClasses' => [],
			'expectedFloatEnumClasses' => [],
			'expectedIntegerEnumClasses' => [],
			'expectedStringEnumClasses' => [],
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
			'expectedBooleanEnumClasses' => [],
			'expectedFloatEnumClasses' => [],
			'expectedIntegerEnumClasses' => [
				FooIntegerEnum::class,
			],
			'expectedStringEnumClasses' => [],
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
			'expectedBooleanEnumClasses' => [
				FooBooleanEnum::class,
			],
			'expectedFloatEnumClasses' => [
				FooFloatEnum::class,
			],
			'expectedIntegerEnumClasses' => [
				FooIntegerEnum::class,
			],
			'expectedStringEnumClasses' => [
				FooStringEnum::class,
			],
		];
	}

	/**
	 * @dataProvider registerDbalTypesDataProvider
	 *
	 * @param mixed[] $configuration
	 * @param string[] $expectedBooleanEnumClasses
	 * @param string[] $expectedFloatEnumClasses
	 * @param string[] $expectedIntegerEnumClasses
	 * @param string[] $expectedStringEnumClasses
	 */
	public function testRegisterDbalTypes(
		array $configuration,
		array $expectedBooleanEnumClasses,
		array $expectedFloatEnumClasses,
		array $expectedIntegerEnumClasses,
		array $expectedStringEnumClasses
	): void
	{
		$containerBuilder = self::createContainerBuilder($configuration);

		self::assertContainerHasParameter($containerBuilder, 'consistence.doctrine.enum.dbal_types.boolean');
		self::assertArraysContainExactlySameValues($expectedBooleanEnumClasses, $containerBuilder->getParameter('consistence.doctrine.enum.dbal_types.boolean'));

		self::assertContainerHasParameter($containerBuilder, 'consistence.doctrine.enum.dbal_types.float');
		self::assertArraysContainExactlySameValues($expectedFloatEnumClasses, $containerBuilder->getParameter('consistence.doctrine.enum.dbal_types.float'));

		self::assertContainerHasParameter($containerBuilder, 'consistence.doctrine.enum.dbal_types.integer');
		self::assertArraysContainExactlySameValues($expectedIntegerEnumClasses, $containerBuilder->getParameter('consistence.doctrine.enum.dbal_types.integer'));

		self::assertContainerHasParameter($containerBuilder, 'consistence.doctrine.enum.dbal_types.string');
		self::assertArraysContainExactlySameValues($expectedStringEnumClasses, $containerBuilder->getParameter('consistence.doctrine.enum.dbal_types.string'));

		$containerBuilder->compile();
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDbalTypesClassListWithDuplicateClassesDataProvider(): Generator
	{
		yield 'duplicate enum boolean class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'boolean' => [
							FooBooleanEnum::class,
							FooBooleanEnum::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				FooBooleanEnum::class,
			],
		];

		yield 'duplicate enum float class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'float' => [
							FooFloatEnum::class,
							FooFloatEnum::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				FooFloatEnum::class,
			],
		];

		yield 'duplicate enum integer class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'integer' => [
							FooIntegerEnum::class,
							FooIntegerEnum::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				FooIntegerEnum::class,
			],
		];

		yield 'duplicate enum string class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'string' => [
							FooStringEnum::class,
							FooStringEnum::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				FooStringEnum::class,
			],
		];
	}

	/**
	 * @dataProvider enumDbalTypesClassListWithDuplicateClassesDataProvider
	 *
	 * @param mixed[] $configuration
	 * @param string[] $expectedDuplicateClasses
	 */
	public function testEnumDbalTypesClassListWitDuplicateClasses(
		array $configuration,
		array $expectedDuplicateClasses
	): void
	{
		try {
			self::createContainerBuilder($configuration);

			Assert::fail('Exception expected');
		} catch (\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException $e) {
			Assert::assertInstanceOf(
				\Consistence\Doctrine\SymfonyBundle\DependencyInjection\EnumDbalTypesClassListCannotContainDuplicateClassesException::class,
				$e->getPrevious()
			);

			foreach ($expectedDuplicateClasses as $expectedClass) {
				Assert::assertContains($expectedClass, $e->getPrevious()->getDuplicateClasses());
			}
			Assert::assertCount(count($expectedDuplicateClasses), $e->getPrevious()->getDuplicateClasses());
		}
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDbalTypesClassListWithNotEnumClassesDataProvider(): Generator
	{
		yield 'duplicate enum boolean class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'boolean' => [
							DateTimeImmutable::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				DateTimeImmutable::class,
			],
		];

		yield 'duplicate enum float class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'float' => [
							DateTimeImmutable::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				DateTimeImmutable::class,
			],
		];

		yield 'duplicate enum integer class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'integer' => [
							DateTimeImmutable::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				DateTimeImmutable::class,
			],
		];

		yield 'duplicate enum string class' => [
			'configuration' => [
				'enum' => [
					'dbal_types' => [
						'string' => [
							DateTimeImmutable::class,
						],
					],
				],
			],
			'expectedDuplicateClasses' => [
				DateTimeImmutable::class,
			],
		];
	}

	/**
	 * @dataProvider enumDbalTypesClassListWithNotEnumClassesDataProvider
	 *
	 * @param mixed[] $configuration
	 * @param string[] $expectedNotEnumClasses
	 */
	public function testEnumDbalTypesClassListWithNotEnumClasses(
		array $configuration,
		array $expectedNotEnumClasses
	): void
	{
		try {
			self::createContainerBuilder($configuration);

			Assert::fail('Exception expected');
		} catch (\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException $e) {
			Assert::assertInstanceOf(
				\Consistence\Doctrine\SymfonyBundle\DependencyInjection\EnumDbalTypesClassListCannotContainNonEnumClassesException::class,
				$e->getPrevious()
			);

			foreach ($expectedNotEnumClasses as $expectedClass) {
				Assert::assertContains($expectedClass, $e->getPrevious()->getNotEnumClasses());
			}
			Assert::assertCount(count($expectedNotEnumClasses), $e->getPrevious()->getNotEnumClasses());
		}
	}

	/**
	 * @param mixed[] $configuration
	 * @return \Symfony\Component\DependencyInjection\ContainerBuilder
	 */
	private static function createContainerBuilder(array $configuration): ContainerBuilder
	{
		$configuration = [
			'consistence_doctrine' => $configuration,
		];

		$containerBuilder = new ContainerBuilder();
		$containerBuilder->registerExtension(new ConsistenceDoctrineExtension());

		foreach ($containerBuilder->getExtensions() as $extension) {
			$extension->load($configuration, $containerBuilder);
		}

		return $containerBuilder;
	}

	private static function assertContainerHasParameter(Container $container, string $parameterName): void
	{
		Assert::assertTrue($container->hasParameter($parameterName), sprintf('Container does not have parameter `%s`', $parameterName));
	}

	/**
	 * @param mixed[] $expectedArray
	 * @param mixed[] $actualArray
	 */
	private static function assertArraysContainExactlySameValues(array $expectedArray, array $actualArray): void
	{
		foreach ($expectedArray as $expectedArrayValue) {
			Assert::assertContains($expectedArrayValue, $actualArray);
		}

		Assert::assertCount(count($expectedArray), $actualArray);
	}

}
