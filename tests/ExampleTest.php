<?php

namespace Adeliom\WP\Extensions\Tests;

use Adeliom\WP\Extensions\Extensions;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    protected function getPackageProviders($app)
    {
        return [Extensions::class];
    }
}
