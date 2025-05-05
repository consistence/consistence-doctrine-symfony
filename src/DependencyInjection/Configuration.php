<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\SymfonyBundle\DependencyInjection;

use Consistence\Enum\Enum;
use Consistence\Type\ArrayType\ArrayType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration
	extends \Consistence\ObjectPrototype
	implements \Symfony\Component\Config\Definition\ConfigurationInterface
{

	/** @var string */
	private $rootNode;

	public function __construct(
		string $rootNode
	)
	{
		$this->rootNode = $rootNode;
	}

	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder($this->rootNode);
		$rootNode = $treeBuilder->getRootNode();

		$rootNode->children()->append($this->createEnumNode('enum'));

		return $treeBuilder;
	}

	private function createEnumNode(string $nodeName): ArrayNodeDefinition
	{
		$node = new ArrayNodeDefinition($nodeName);
		$node->children()->append($this->createEnumDbalTypesNode('dbal_types'));
		$node->addDefaultsIfNotSet();

		return $node;
	}

	private function createEnumDbalTypesNode(string $nodeName): ArrayNodeDefinition
	{
		$node = new ArrayNodeDefinition($nodeName);
		$node->children()->append($this->createEnumClassesNode('boolean'));
		$node->children()->append($this->createEnumClassesNode('float'));
		$node->children()->append($this->createEnumClassesNode('integer'));
		$node->children()->append($this->createEnumClassesNode('string'));
		$node->addDefaultsIfNotSet();

		return $node;
	}

	private function createEnumClassesNode(string $nodeName): ArrayNodeDefinition
	{
		$node = new ArrayNodeDefinition($nodeName);
		$node->prototype('scalar');
		$node
			->validate()
			->ifTrue(function (array $enumClasses): bool {
				return count($enumClasses) !== count(ArrayType::uniqueValues($enumClasses))
					|| ArrayType::containsValueByValueCallback($enumClasses, function ($enumClass): bool {
						return !is_string($enumClass)
							|| !$this->isEnumClass($enumClass);
					});
			})
			->then(function (array $enumClasses): void {
				$frequency = array_count_values($enumClasses);
				$duplicateClasses = array_keys(ArrayType::filterValuesByCallback($frequency, static function (int $count): bool {
					return $count > 1;
				}));
				if (count($duplicateClasses) > 0) {
					throw new \Consistence\Doctrine\SymfonyBundle\DependencyInjection\EnumDbalTypesClassListCannotContainDuplicateClassesException($duplicateClasses);
				}

				$notEnumClasses = ArrayType::filterValuesByCallback($enumClasses, function ($enumClass): bool {
					return !is_string($enumClass)
						|| !$this->isEnumClass($enumClass);
				});
				if (count($notEnumClasses) > 0) {
					throw new \Consistence\Doctrine\SymfonyBundle\DependencyInjection\EnumDbalTypesClassListCannotContainNonEnumClassesException($notEnumClasses);
				}

				// @codeCoverageIgnoreStart
				// should be unreachable code
				throw new \Exception('Unexpected case');
				// @codeCoverageIgnoreEnd
			});

		return $node;
	}

	private function isEnumClass(string $class): bool
	{
		return is_a($class, Enum::class, true);
	}

}
