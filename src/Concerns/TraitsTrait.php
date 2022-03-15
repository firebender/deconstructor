<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass;

trait TraitsTrait
{
    /**
     * @return array<int, mixed>
     */
    public function traits(object $object): array
    {
        $class = new ReflectionClass($object);

        $traits = array_values($this->class_uses_deep($class->name));

        sort($traits);

        return $traits;
    }

    /**
     * Gets all traits a class uses, including those of parents'.
     *
     * https://www.php.net/manual/en/function.class-uses.php#110752
     *
     * @return array<string, string>
     */
    protected function class_uses_deep(string $class, bool $autoload = true): array
    {
        $traits = [];

        do {
            $uses = class_uses($class, $autoload);
            assert(is_array($uses));
            $traits = array_merge($uses, $traits);
        } while ($class = get_parent_class($class));

        foreach ($traits as $trait => $same) {
            $uses = class_uses($trait, $autoload);
            assert(is_array($uses));
            $traits = array_merge($uses, $traits);
        }

        return array_unique($traits);
    }
}
