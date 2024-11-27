<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Consistence\Doctrine\Enum\Type\BooleanEnumType;
use Consistence\Doctrine\Enum\Type\FloatEnumType;
use Consistence\Doctrine\Enum\Type\IntegerEnumType;
use Consistence\Doctrine\Enum\Type\StringEnumType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConsistenceDoctrineExtension
	extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
	implements \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface
{

	public const ALIAS = 'consistence_doctrine';

	public const DOCTRINE_BUNDLE_ALIAS = 'doctrine';

	public function prepend(ContainerBuilder $container): void
	{
		if (!$container->hasExtension(self::DOCTRINE_BUNDLE_ALIAS)) {
			throw new \Consistence\Doctrine\SymfonyBundle\DependencyInjection\DoctrineBundleRequiredException();
		}

		$container->loadFromExtension(self::DOCTRINE_BUNDLE_ALIAS, [
			'dbal' => [
				'types' => [
					BooleanEnumType::NAME => BooleanEnumType::class,
					FloatEnumType::NAME => FloatEnumType::class,
					IntegerEnumType::NAME => IntegerEnumType::class,
					StringEnumType::NAME => StringEnumType::class,
				],
			],
		]);
	}

	/**
	 * @param mixed[][] $configs
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
		$loader->load('services.yaml');
	}

	public function getAlias(): string
	{
		return self::ALIAS;
	}

}
