<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Traits;

trait Parents
{
	/**
	 * 
	 */
	public function parents(Object $object)
	{
		$parents = array_reverse(array_values(class_parents($object)));

		return $parents;
	}
}