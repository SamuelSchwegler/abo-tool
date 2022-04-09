<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public ?User $admin;
    public ?User $customer;

    protected static bool $seeded = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$seeded) {
            Artisan::call('migrate:fresh --seed');

            self::$seeded = true;
        }

        $this->admin = User::where('email', 'demo@webtheke.ch')->first();
        $this->customer = User::role('customer')->first();
    }
}
