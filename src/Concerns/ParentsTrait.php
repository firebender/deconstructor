<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

trait ParentsTrait
{
    /**
     * @return array<int, mixed>
     */
    public function parents(object $object): array
    {
        $class_parents = class_parents($object);
        if ($class_parents === false) {
            return [];
        }

        $parents = array_reverse(array_values($class_parents));

        return $parents;
    }
}
