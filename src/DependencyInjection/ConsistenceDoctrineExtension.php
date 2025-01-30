<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsistenceDoctrineExtension extends \Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension
{

	use \Consistence\Type\ObjectMixinTrait;

	/**
	 * @param mixed[] $mergedConfig
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
	{
		$container->setParameter(
			'consistence.doctrine.enum.dbal_types.boolean',
			$mergedConfig
				['enum']
				['dbal_types']
				['boolean']
		);
		$container->setParameter(
			'consistence.doctrine.enum.dbal_types.float',
			$mergedConfig
				['enum']
				['dbal_types']
				['float']
		);
		$container->setParameter(
			'consistence.doctrine.enum.dbal_types.integer',
			$mergedConfig
				['enum']
				['dbal_types']
				['integer']
		);
		$container->setParameter(
			'consistence.doctrine.enum.dbal_types.string',
			$mergedConfig
				['enum']
				['dbal_types']
				['string']
		);
	}

	/**
	 * @param mixed[] $config
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \Consistence\Doctrine\SymfonyBundle\DependencyInjection\Configuration
	 */
	public function getConfiguration(array $config, ContainerBuilder $container): Configuration
	{
		return new Configuration(
			$this->getAlias()
		);
	}

}
