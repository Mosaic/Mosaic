<?php

namespace Fresco\Definitions;

use Fresco\Contracts\Routing\RouteLoader;
use Fresco\Routing\Loaders\LoadRoutesFromFile;
use Interop\Container\Definition\DefinitionProviderInterface;

class RouteFileDefinition implements DefinitionProviderInterface
{
    /**
     * Returns the definition to register in the container.
     *
     * Definitions must be indexed by their entry ID. For example:
     *
     *     return [
     *         'logger' => ...
     *         'mailer' => ...
     *     ];
     *
     * @return array
     */
    public function getDefinitions()
    {
        return [
            RouteLoader::class => function () {
                return new LoadRoutesFromFile;
            }
        ];
    }
}