<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle;

use Consistence\Doctrine\Enum\Type\BooleanEnumType;
use Consistence\Doctrine\Enum\Type\FloatEnumType;
use Consistence\Doctrine\Enum\Type\IntegerEnumType;
use Consistence\Doctrine\Enum\Type\StringEnumType;
use Doctrine\DBAL\Types\Type as DbalType;

class ConsistenceDoctrineBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{

	public function boot(): void
	{
		parent::boot();

		$this->registerClassesForEnumDbalType(BooleanEnumType::class, $this->container->getParameter('consistence.doctrine.enum.dbal_types.boolean'));
		$this->registerClassesForEnumDbalType(FloatEnumType::class, $this->container->getParameter('consistence.doctrine.enum.dbal_types.float'));
		$this->registerClassesForEnumDbalType(IntegerEnumType::class, $this->container->getParameter('consistence.doctrine.enum.dbal_types.integer'));
		$this->registerClassesForEnumDbalType(StringEnumType::class, $this->container->getParameter('consistence.doctrine.enum.dbal_types.string'));
	}

	/**
	 * @param string $dbalTypeClass
	 * @param string[] $enumClasses
	 */
	private function registerClassesForEnumDbalType(
		string $dbalTypeClass,
		array $enumClasses
	): void
	{
		foreach ($enumClasses as $enumClass) {
			$dbalType = $dbalTypeClass::create($enumClass);
			if (DbalType::getTypeRegistry()->has($dbalType->getName())) {
				DbalType::getTypeRegistry()->override($dbalType->getName(), $dbalType);
			} else {
				DbalType::getTypeRegistry()->register($dbalType->getName(), $dbalType);
			}
		}
	}

}
