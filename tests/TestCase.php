<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Test chạy trên MariaDB thật (catalog_cars_test, khai trong phpunit.xml),
    // không phải sqlite :memory: — schema dựa nặng vào cột json và enum.
    use RefreshDatabase;
}
