<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Container;

use Hyperf\Config\ProviderConfig;
use Hyperf\Di\Exception\Exception;
use SwooleTW\Hyperf\Container\DefinitionSource;

class DefinitionSourceFactory
{
    public function __invoke(): DefinitionSource
    {
        if (! defined('BASE_PATH')) {
            throw new Exception('BASE_PATH is not defined.');
        }

        $configFromProviders = [];
        if (class_exists(ProviderConfig::class)) {
            $configFromProviders = ProviderConfig::load();
        }

        $serverDependencies = $configFromProviders['dependencies'] ?? [];
        $dependenciesPath = BASE_PATH . '/config/autoload/dependencies.php';
        if (file_exists($dependenciesPath)) {
            $definitions = include $dependenciesPath;
            $serverDependencies = array_replace($serverDependencies, $definitions ?? []);
        }

        return new DefinitionSource($serverDependencies);
    }
}
