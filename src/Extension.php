<?php

declare(strict_types=1);

/**
 * This file is part of Larastan.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Larastan;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;

/**
 * @internal
 */
final class Extension implements MethodsClassReflectionExtension, BrokerAwareExtension
{
    use Concerns\HasBroker;

    /**
     * @var \PHPStan\Reflection\Php\PhpMethodReflectionFactory
     */
    private $methodReflectionFactory;

    /**
     * @var \NunoMaduro\Larastan\Kernel
     */
    private $kernel;

    /**
     * Extension constructor.
     *
     * @param \PHPStan\Reflection\Php\PhpMethodReflectionFactory $methodReflectionFactory
     * @param \NunoMaduro\Larastan\Kernel|null $kernel
     */
    public function __construct(PhpMethodReflectionFactory $methodReflectionFactory, Kernel $kernel = null)
    {
        $this->methodReflectionFactory = $methodReflectionFactory;
        $this->kernel = $kernel ?? new Kernel($methodReflectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $this->kernel->handle($this->broker, $classReflection, $methodName)
            ->hasFound();
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->kernel->handle($this->broker, $classReflection, $methodName)
            ->getMethodReflection();
    }
}