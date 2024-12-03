<?php

declare(strict_types = 1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

$config = $config->enableAnalysisOfUnusedDevDependencies();
$config = $config->addPathToScan(__DIR__, true);

// dependency from YAML configuration only
$config = $config->ignoreErrorsOnPackages([
	'doctrine/doctrine-bundle', // e.g. dependency on @annotation_reader, postLoad event etc.
], [ErrorType::PROD_DEPENDENCY_ONLY_IN_DEV]);

// opt-in Symfony functionality
$config = $config->ignoreErrorsOnPackages([
	'symfony/yaml',
], [ErrorType::UNUSED_DEPENDENCY]);

// tools
$config = $config->ignoreErrorsOnPackages([
	'consistence/coding-standard',
	'phing/phing',
	'php-parallel-lint/php-console-highlighter',
	'php-parallel-lint/php-parallel-lint',
], [ErrorType::UNUSED_DEPENDENCY]);

return $config;
