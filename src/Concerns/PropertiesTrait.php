<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass, ReflectionFunction, ReflectionProperty;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

trait PropertiesTrait
{
	/**
	 * 
	 */
	public function properties(Object $object)
	{
        $class = new ReflectionClass($object);

        return $class->getProperties();
	}

    /**
     * 
     */
    protected function getDump($value)
    {
        $type = gettype($value);

        if ($type === 'object') return get_class($value);

        if ($type === 'array') $value = self::stringifyClosureInArray($value);

        $cloner = new VarCloner();
        $dumper = new CliDumper();

        $data = $cloner->cloneVar($value);

        $output = $dumper->dump($data, true);

        $output = str_replace(PHP_EOL, '', $output);

        // remove array:\d+
        $pattern = '|array:\d+\s|';
        $output = preg_replace($pattern, '', $output);

        // remove extra space after []
        $pattern = '|\[\s+|';
        $output = preg_replace($pattern, '[', $output);

        $pattern = '|\s{2}|';
        $output = preg_replace($pattern, ', ', $output);

        return $output;
    }

    /**
     * Closures in arrays return too much info
     * Turn closures into string before passing to symfony/var-dumper
     */
    protected function stringifyClosureInArray($array)
    {
        foreach ($array as $k => $v)
        {
            if (is_object($v))
            {
                $class = new ReflectionClass($v);
                $name = $class->getName();
                if ($name === 'Closure')
                {
                    $arr = [];
                    $function = new ReflectionFunction($v);
                    $params = $function->getParameters();
                    foreach ($params as $param)
                    {
                        $entry = '';

                        if ($param->hasType())
                        {
                            $type = $param->getType();
                            $entry .= $type->getName() . ' ';
                        }

                        $entry .= $param->getName();

                        if ($param->isDefaultValueAvailable())
                        {
                            if ($param->isDefaultValueConstant())
                            {
                                $entry .= ' = ' . $param->getDefaultValueConstantName();
                                continue;
                            }

                            $defaultValue = $param->getDefaultValue();
                            $dumped = self::getDump($defaultValue);
                            $entry .= ' = ' . $dumped;
                        }

                        $arr[] = $entry;

                        $array[$k] = 'Closure (' . implode(', ', $arr) . ')';
                    }
                }
            }
        }

        return $array;
    }

    /**
     * 
     */
    protected function getPropertyModifiers(ReflectionProperty $property)
    {
            $modifier = '';

            if ($property->isPrivate()) {
                $modifier .= '<fg=blue;options=bold>private</> ';
            } elseif ($property->isProtected()) {
                $modifier .= '<fg=magenta;options=bold>protected</> ';
            } else {
                $modifier .= '<fg=green;options=bold>public</> ';
            }

            $modifier .= $property->isStatic() ? 'static ' : '' ;

            return $modifier;
    }

    /**
     * 
     */
    protected function formattedProperties(Object $object)
    {
        $properties = [];

        $arr = $this->properties($object);

        foreach ($arr as $property)
        {
            $name = $property->getName();

            if ($name === 'macros') $this->flag = true;

            $modifier = $this->getPropertyModifiers($property);

            $type = '';
            if ($property->hasType())
            {
                $type = $property->getType() . ' ';

            }

            $property->setAccessible(true);

            $default = '';

            if ($property->hasDefaultValue()) {
                $value = $property->getDefaultValue();
                $value = $this->getDump($value);
                $value .= ' = ' . $value;
                $default .= ' = ' . $value;
            }

            $properties[$name] = $modifier . $type . '$' . $name . $default;
        }

        ksort($properties);

        return $properties;
    }

}