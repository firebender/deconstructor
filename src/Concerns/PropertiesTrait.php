<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass, ReflectionFunction, ReflectionProperty, ReflectionType, ReflectionParameter;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Closure;

trait PropertiesTrait
{
	/**
	 * @return array<int, mixed>
	 */
	public function properties(Object $object): array
	{
        $class = new ReflectionClass($object);

        return $class->getProperties();
	}

    /**
     * @param mixed $value
     * @return string|null
     */
    protected function getDump(mixed $value): string|null
    {
        if (is_object($value) === true) {
            return get_class($value);
        }

        $value = self::stringifyClosureInArray($value);

        $cloner = new VarCloner();
        $dumper = new CliDumper();

        $data = $cloner->cloneVar($value);

        $output = $dumper->dump($data, true);

        if ($output !== null) {
            $output = str_replace(PHP_EOL, '', $output);
        }

        if ($output !== null) {
            // remove array:\d+
            $pattern = '|array:\d+\s|';
            $output = preg_replace($pattern, '', $output);
        }

        if ($output !== null) {
            // remove extra space after []
            $pattern = '|\[\s+|';
            $output = preg_replace($pattern, '[', $output);
        }

        if ($output !== null) {
            $pattern = '|\s{2}|';
            $output = preg_replace($pattern, ', ', $output);
        }

        return $output;
    }

    /**
     * Closures in arrays return too much info
     * Turn closures into string before passing to symfony/var-dumper
     *
     * @param mixed $array
     * @return array<int, string>
     */
    protected function stringifyClosureInArray(mixed $array): array
    {
        if (is_array($array) === false) return [];

        foreach ($array as $k => $v)
        {
            assert($v instanceof Closure);

            $arr = [];
            $function = new ReflectionFunction($v);
            $params = $function->getParameters();
            foreach ($params as $param)
            {
                $entry = '';

                if ($param->hasType())
                {
                    $type = $param->getType();
                    assert($type instanceof ReflectionType);
                    assert(method_exists($type, 'getName'));
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

        return $array;
    }

    /**
     * 
     */
    protected function getPropertyModifiers(ReflectionProperty $property): string
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
     * @return array<int|string, string>
     */
    protected function formattedProperties(object $object): array
    {
        $properties = [];

        $arr = $this->properties($object);

        foreach ($arr as $property)
        {
            assert($property instanceof ReflectionProperty);
            
            $name = $property->getName();

            // if ($name === 'macros') $this->flag = true;

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
                $default .= ' = ' . $value;
            }

            $styledName = '<fg=#EEED09>$' . $name . '</>';
            $properties[$name] = $modifier . $type . $styledName . $default;
        }

        ksort($properties);

        return $properties;
    }

}