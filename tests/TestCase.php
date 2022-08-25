<?php

namespace TheRiptide\LaravelDynamicDashboard\Tests;

use TheRiptide\LaravelDynamicDashboard\DynamicDashboardServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app)
  {
    return [
      DynamicDashboardServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // perform environment setup
  }

  public function createApplication()
  {

  }
}