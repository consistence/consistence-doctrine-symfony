<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Consistence\Enum\Enum;

class EnumDbalTypesClassListCannotContainNonEnumClassesException extends \Consistence\PhpException
{

	/** @var string[] */
	private $notEnumClasses;

	/**
	 * @param string[] $notEnumClasses
	 * @param \Throwable|null $previous
	 */
	public function __construct(array $notEnumClasses, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'These classes are not %s classes: %s',
			Enum::class,
			implode(', ', $notEnumClasses)
		), $previous);
		$this->notEnumClasses = $notEnumClasses;
	}

	/**
	 * @return string[]
	 */
	public function getNotEnumClasses(): array
	{
		return $this->notEnumClasses;
	}

}
