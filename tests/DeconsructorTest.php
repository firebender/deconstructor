<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use FireBender\Deconstructor;

class DeconstructorTest extends TestCase
{
	/**
	 * @test
	 */
	public function passing_null_returns_deconstructor_object()
	{
		$actual = d();

		$this->assertIsObject($actual);

		$expected = Deconstructor::class;
		$this->assertEquals($expected, get_class($actual));
	}

}