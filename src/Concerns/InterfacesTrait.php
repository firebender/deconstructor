<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass;

trait InterfacesTrait
{
    /**
     * @return array<int, mixed>
     */
    public function interfaces(object $object): array
    {
        $class = new ReflectionClass($object);

        $arr = $class->getInterfaces();

        if (count($arr) === 0) {
            return [];
        }

        $interfaces = [];

        foreach ($arr as $interface) {
            $interfaces[] = $interface->getName();
        }

        sort($interfaces);

        return $interfaces;
    }
}
