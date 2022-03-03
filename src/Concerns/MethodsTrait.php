<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use Illuminate\Support\Arr;
use ReflectionClass, ReflectionMethod, ReflectionType, Reflection;

trait MethodsTrait
{
    /**
     * @var array<int, string>
     */
    protected $magic = [
        '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup',
        '__serialize', '__unserialize', '__toString', '__invoke', '__setState', '__clone', '__debugInfo'
    ];

	/**
	 * @return array<int, mixed>
	 */
	public function methods(object $object): array
	{
        $class = new ReflectionClass($object);

        $arr = $class->getMethods();

        if (count($arr) === 0) return [];

        return $arr;		
	}

	/**
	 * @return array<int|string, string>
	 */
	protected function formattedMethods(object $object): array
	{
        $arr = $this->methods($object);

        $methods = [];

        foreach ($arr as $method) {
            assert($method instanceof ReflectionMethod);

            $name = $method->name;

            $magic = $this->isMagicMethod($name) ? true : false;

            $parameters = $magic === true ? '()' : $this->getMethodParameters($method);

            $modifiers = $magic === true ? '' : $this->getStyledMethodModifiers($method);

            $returnType = $this->getMethodReturnType($method);

            $entry = '';

            if (strlen($modifiers) > 0) $entry .= $modifiers . ' ';

            $styledName = '<fg=#FFD700>' . $name . '</>';
            $methods[$name] = $entry . $styledName . $parameters . $returnType;
        }

        ksort($methods);

        return $methods;
	}

    /**
     * 
     */
    protected function isMagicMethod(string $name): bool
    {
        if (substr($name, 0, 2) === '__' && in_array($name, $this->magic, true)) return true;

        return false;
    }

    /**
     * 
     */
    protected function getMethodParameters(ReflectionMethod $method): string
    {
        $parameters = $method->getParameters();
        if (count($parameters) === 0) return '()';

        $arr = [];          
        foreach ($parameters as $parameter)
        {
            $tmp = [];

            if ($parameter->hasType())
            {
                $type = $parameter->getType();
                assert($type instanceof ReflectionType);

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
     * @param \ReflectionMethod $method
     * @return array<int, string>
     */
    protected function getMethodModifiers(ReflectionMethod $method): array
    {
        return Reflection::getModifierNames($method->getModifiers());
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getStyledMethodModifiers(ReflectionMethod $method): string
    {
        $reflected = $this->getMethodModifiers($method);

        if (in_array('public', $reflected, true)) {
            $modifiers = '<fg=green>';
        } elseif (in_array('protected', $reflected, true)) {
            $modifiers = '<fg=magenta>';
        } elseif (in_array('private', $reflected, true)) {
            $modifiers = '<fg=cyan>';
        } else {
            $modifiers = '<fg=green>';
        }

        $modifiers .= implode(' ', $reflected);

        $modifiers .= '</>';

        return $modifiers;
    }

    /**
     * @return string
     */
    protected function getMethodReturnType(ReflectionMethod $method)
    {
        if ($method->hasReturnType() === false) return '';

        $returnType = $method->getReturnType();
        if ($returnType instanceof ReflectionType === false) return '';

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

        // return ' ' . $returnType;
    }


}