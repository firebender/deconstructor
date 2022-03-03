<?php
declare(strict_types=1);

namespace FireBender\Deconstructor\Tests;

use FireBender\Deconstructor\Deconstructor;

class DeconstructorExceptionTest extends TestCase
{
	/**
	 * @test
	 */
	public function passing_non_object_throws_exception()
	{
		$array = [
			'apple' => 1,
			'boy' => 2,
			'cat' => 3
		];

		$this->expectExceptionMessage('Can deconstruct only objects');

		d($array);
	}

}