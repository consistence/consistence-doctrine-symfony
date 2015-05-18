<?php

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConsistenceDoctrineExtension extends \Symfony\Component\HttpKernel\DependencyInjection\Extension
{

	const ALIAS = 'consistence_doctrine';

	/**
	 * @param mixed[][] $configs
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		// ...
	}

	/**
	 * @return string
	 */
	public function getAlias()
	{
		return self::ALIAS;
	}

}
