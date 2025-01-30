<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

class EnumDbalTypesClassListCannotContainDuplicateClassesException extends \Consistence\PhpException
{

	/** @var string[] */
	private $duplicateClasses;

	/**
	 * @param string[] $duplicateClasses
	 * @param \Throwable|null $previous
	 */
	public function __construct(array $duplicateClasses, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Enum class list contains these multiple times: %s',
			implode(', ', $duplicateClasses)
		), $previous);
		$this->duplicateClasses = $duplicateClasses;
	}

	/**
	 * @return string[]
	 */
	public function getDuplicateClasses(): array
	{
		return $this->duplicateClasses;
	}

}
