<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2016 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */

namespace Slim;

use Closure;
use Interop\Container\ContainerInterface;

class DeferredCallable
{
    use CallableResolverAwareTrait;

    private $callable;
    /** @var  ContainerInterface */
    private $container;

    private $additionalArgs;

    /**
     * DeferredMiddleware constructor.
     * @param callable|string $callable
     * @param ContainerInterface $container
     */
    public function __construct($callable, ContainerInterface $container = null, $additionalArgs = null)
    {
        $this->callable = $callable;
        $this->container = $container;
        $this->additionalArgs = $additionalArgs;
    }

    public function __invoke()
    {
        $callable = $this->resolveCallable($this->callable);
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this->container);
        }

        $args = func_get_args();

        if ($this->additionalArgs && is_array($this->additionalArgs)) {
            $args = array_merge($args, $this->additionalArgs);
        }

        return call_user_func_array($callable, $args);
    }
}
