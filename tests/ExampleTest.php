<?php

namespace Adeliom\WP\Extensions\Tests;

use Adeliom\WP\Extensions\Extensions;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [Extensions::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
