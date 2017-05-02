<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

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

	const ALIAS = 'consistence_doctrine';

	const DOCTRINE_BUNDLE_ALIAS = 'doctrine';

	public function prepend(ContainerBuilder $container)
	{
		if (!$container->hasExtension(self::DOCTRINE_BUNDLE_ALIAS)) {
			throw new \Consistence\Doctrine\SymfonyBundle\DependencyInjection\DoctrineBundleRequiredException();
		}

		$container->loadFromExtension(self::DOCTRINE_BUNDLE_ALIAS, [
			'dbal' => [
				'types' => [
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
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
		$loader->load('services.yml');
	}

	public function getAlias(): string
	{
		return self::ALIAS;
	}

}
