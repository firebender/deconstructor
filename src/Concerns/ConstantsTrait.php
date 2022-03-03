<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Concerns;

use ReflectionClass;

trait ConstantsTrait
{
	/**
	 * @return array<string, mixed>
	 */
	public function constants(object $object): array
	{
        $class = new ReflectionClass($object);

        $constants = $class->getConstants();
        ksort($constants);

        return $constants;
	}

    /**
     * @return array<int, mixed>
     */
    protected function formattedConstants(object $object): array
    {
        $constants = $this->constants($object);

        $display = [];
        foreach ($constants as $key => $item) {
            $entry = '';
            if (is_array($item)) {
                $s = "[" . PHP_EOL;
                foreach ($item as $k => $v) {
                    $s .= "$k => $v" . PHP_EOL;
                }
                $s .= "]" . PHP_EOL;
                $entry = $s;
            } else if (is_string($item)) {
                $entry = $item;
            }

            $format = '<fg=blue;options=bold>%s</> => %s';
            $entry = sprintf($format, $key, $entry);
            $display[] = $entry;
        }

        return $display;
    }

}