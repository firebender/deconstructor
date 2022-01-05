<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Traits;
use ReflectionClass;

trait Traits
{
	/**
	 * 
	 */
	public function traits(Object $object)
	{
        $class = new ReflectionClass($object);

        $traits = array_values($this->class_uses_deep($class->name));

        sort($traits);

        return $traits;
	}

    /**
     * Gets all traits a class uses, including those of parents'
     * 
     * https://www.php.net/manual/en/function.class-uses.php#110752
     */
    protected function class_uses_deep($class, $autoload = true) 
    {
        $traits = [];
        
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while($class = get_parent_class($class));

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }
        
        return array_unique($traits);
    }

}