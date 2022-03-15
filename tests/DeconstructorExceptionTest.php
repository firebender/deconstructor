<?php

declare(strict_types=1);

namespace FireBender\Deconstructor\Tests;

class DeconstructorExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function passing_non_object_throws_exception(): void
    {
        $array = [
            'apple' => 1,
            'boy'   => 2,
            'cat'   => 3,
        ];

        $this->expectExceptionMessage('Can deconstruct only objects');

        d($array);
    }
}
