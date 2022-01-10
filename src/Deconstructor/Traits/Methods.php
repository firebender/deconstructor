<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Traits;

use Illuminate\Support\Arr;
use ReflectionClass, ReflectionMethod, Reflection;

trait Methods
{
    /**
     * 
     */
    protected $magic = [
        '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup',
        '__serialize', '__unserialize', '__toString', '__invoke', '__setState', '__clone', '__debugInfo'
    ];

	/**
	 * 
	 */
	public function methods(Object $object)
	{
        $class = new ReflectionClass($object);

        $arr = $class->getMethods();

        if (!count($arr)) return [];

        return $arr;		
	}

	/**
	 * 
	 */
	protected function formattedMethods(Object $object)
	{
        $arr = $this->methods($object);

        $methods = [];

        foreach ($arr as $method) {

            $name = $method->name;

            $magic = $this->isMagicMethod($name) ? true : false;

            $parameters = $magic === true ? '()' : $this->getMethodParameters($method);

            $modifiers = $magic === true ? '' : $this->getMethodModifiers($method);

            $returnType = $this->getMethodReturnType($method);

            $entry = '';

            if (strlen($modifiers)) $entry .= $modifiers . ' ';

            $methods[$name] = $entry . $name . $parameters . $returnType;
        }

        ksort($methods);

        return $methods;
	}

    /**
     * 
     */
    protected function isMagicMethod($name)
    {
        if (substr($name, 0, 2) === '__' && in_array($name, $this->magic)) return true;

        return false;
    }

    /**
     * 
     */
    protected function getMethodParameters(ReflectionMethod $method)
    {
        $parameters = $method->getParameters();
        if (!count($parameters)) return '()';

        $arr = [];          
        foreach ($parameters as $parameter)
        {
            $tmp = [];

            if ($parameter->hasType())
            {
                $type = $parameter->getType();

                if (method_exists($type, 'getName')) {
                    $tmp[] = $type->getName();
                } elseif (property_exists($type, 'name')) {
                    $tmp[] = $type->name;
                }

            }

            $name = $parameter->getName();
            $tmp[] = '$' . $name;

            if ($parameter->isDefaultValueAvailable())
            {
                if ($parameter->isDefaultValueConstant())
                {
                    $tmp[] = ' = ' . $parameter->getDefaultValueConstantName();
                    continue;
                }

                $defaultValue = $parameter->getDefaultValue();

                if ($parameter->isVariadic())
                {
                    $defaultValue = '...' . $defaultValue;

                }

                $defaultValue = $this->getDump($defaultValue);
            }

            $arr[] = implode(' ', $tmp);
        }

        return '(' . implode(', ', $arr) . ')';
    }

    /**
     * 
     */
    protected function getMethodModifiers(ReflectionMethod $method)
    {
        $arr = Reflection::getModifierNames($method->getModifiers());

        $names = implode(' ', $arr);

        if (in_array('public', $arr)) {
            $modifiers = "<fg=green;options=bold>$names</>";
        } elseif (in_array('protected', $arr)) {
            $modifiers = "<fg=magenta;options=bold>$names</>";
        } elseif (in_array('private', $arr)) {
            $modifiers = "<fg=blue;options=bold>$names</>";
        } else {
            $modifiers = "<fg=green;options=bold>$names</>";
        }

        return $modifiers;
    }

    /**
     * 
     */
    protected function getMethodReturnType(ReflectionMethod $method)
    {
        if (!$method->hasReturnType()) return '';

        $returnType = $method->getReturnType();

        if ($returnType->allowsNull()) return ' : null';

        if (method_exists($returnType, 'getName')) {
            switch ($returnType->getName())
            {
                case 'void':
                    return '';
                case 'bool':
                    return ' : bool';
                default:
                    return ' : ' . $returnType->getName();
            }
        } else {
            return ' : ' . $returnType->__toString();
        }

        return ' ' . $returnType;
    }


}