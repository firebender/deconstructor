<?php
declare(strict_types=1);

namespace FireBender\Deconstructor\Tests;

use FireBender\Deconstructor\Deconstructor;

class DeconstructorTest extends TestCase
{
	/**
	 * @test
	 */
	public function passing_null_returns_deconstructor_object()
	{
		$deconstructor = d();
		$this->assertIsObject($deconstructor);

		$expected = Deconstructor::class;
		$this->assertEquals($expected, get_class($deconstructor));
	}

	/**
	 * @test
	 */
	public function deconstructor_has_various_sections()
	{
		$deconstructor = d();

		$analysis = d($deconstructor, true);

		$this->assertArrayHasKey('constants', $analysis);
		$this->assertArrayHasKey('interfaces', $analysis);
		$this->assertArrayHasKey('io', $analysis);
		$this->assertArrayHasKey('methods', $analysis);
		$this->assertArrayHasKey('object', $analysis);
		$this->assertArrayHasKey('parents', $analysis);
		$this->assertArrayHasKey('properties', $analysis);
		$this->assertArrayHasKey('traits', $analysis);
	}

}