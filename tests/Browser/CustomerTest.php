<?php

namespace Tests\Browser;

use App\Models\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerTest extends DuskTestCase
{
    use WithFaker;

    public function testCustomers()
    {
        $this->browse(function (Browser $browser) {
            // customer fabrizieren der bestellungen braucht
            $customer = Customer::has('buys', 1)->has('orders')->orderBy('last_name', 'asc')->first();

            $browser->loginAs($this->admin)->visit('/customers')
                ->waitForText('Kunden', 2)
                ->assertSee($customer->name)
                ->screenshot('kunden');
        });
    }
}
