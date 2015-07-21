<?php

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Consistence\Doctrine\Enum\EnumPostLoadEntityListener;
use Consistence\Doctrine\Enum\Type\FloatEnumType;
use Consistence\Doctrine\Enum\Type\IntegerEnumType;
use Consistence\Doctrine\Enum\Type\StringEnumType;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsistenceDoctrineExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var string[] */
	private static $enumTypes = [
		FloatEnumType::NAME => FloatEnumType::class,
		IntegerEnumType::NAME => IntegerEnumType::class,
		StringEnumType::NAME => StringEnumType::class,
	];

	public function testDependsOnDoctrineBundle()
	{
		$containerBuilder = new ContainerBuilder();
		$extension = new ConsistenceDoctrineExtension();

		$this->expectException(\Consistence\Doctrine\SymfonyBundle\DependencyInjection\DoctrineBundleRequiredException::class);
		$extension->prepend($containerBuilder);
	}

	public function testRegisterEnumTypes()
	{
		$types = $this->getDoctrineTypesConfig();
		$this->assertTypes(self::$enumTypes, $types);
	}

	public function testRegisterPostLoadEntityListener()
	{
		$containerBuilder = new ContainerBuilder();
		$extension = new ConsistenceDoctrineExtension();
		$extension->load([], $containerBuilder);

		$this->assertTrue($containerBuilder->has('consistence.doctrine.enum.enum_post_load_entity_listener'));
		$this->assertEquals(EnumPostLoadEntityListener::class, $containerBuilder->getDefinition('consistence.doctrine.enum.enum_post_load_entity_listener')->getClass());
	}

	/**
	 * @return string[] format: type name (string) => type class)
	 */
	private function getDoctrineTypesConfig()
	{
		$doctrineExtension = new DoctrineExtension();
		$extension = new ConsistenceDoctrineExtension();

		$containerBuilder = new ContainerBuilder();
		$containerBuilder->registerExtension($doctrineExtension);
		$containerBuilder->registerExtension($extension);

		$extension->prepend($containerBuilder);

		$doctrineConfig = $containerBuilder->getExtensionConfig($doctrineExtension->getAlias());

		if (!isset($doctrineConfig[0]) || !isset($doctrineConfig[0]['dbal']) || !isset($doctrineConfig[0]['dbal']['types'])) {
			return [];
		}

		return $containerBuilder->getExtensionConfig($doctrineExtension->getAlias())[0]['dbal']['types'];
	}

	/**
	 * @param string[] $expectedTypes format: type name (string) => type class)
	 * @param string[] $actualTypes format: type name (string) => type class)
	 */
	private function assertTypes(array $expectedTypes, array $actualTypes)
	{
		foreach ($expectedTypes as $typeName => $typeClass) {
			$this->assertArraySubset([$typeName => $typeClass], $actualTypes);
		}
		$this->assertCount(count($expectedTypes), $actualTypes);
	}

}
