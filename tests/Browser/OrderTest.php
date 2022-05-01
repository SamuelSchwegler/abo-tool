<?php

namespace Tests\Browser;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrderTest extends DuskTestCase
{
    use WithFaker;

    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testUnauthenticatedOrder()
    {
        $this->browse(function (Browser $browser) {
            $email = $this->faker->safeEmail();
            $user_count = User::count();
            $customer_count = Customer::count();
            $addresses_count = Address::count();

            $browser->visit('/')->waitForText('Gemüseabo gross', 2);
            $browser->click('.order-button')
                ->assertPathBeginsWith('/bundle/buy')
                ->assertSee('Um die Liefertermine zu verwalten brauchen Sie ein Kundenkonto. In diesem Schritt können Sie gleich ein Konto erstellen.');
            $browser->click('button#proceed')
                ->assertPathBeginsWith('/bundle/buy')
                ->pause(50)
                ->assertSee('Passwort muss ausgefüllt sein.');

            // Ausfüllen Daten
            $browser->type('email', $email)
                ->type('password', $this->faker->randomAscii())
                ->type('last_name', $this->faker->lastName());

            $browser->click('button#proceed')
                ->assertPathBeginsWith('/bundle/buy')
                ->pause(150)
                ->assertDontSee('Passwort muss ausgefüllt sein.') // Passwort Fehler ist weg
                ->assertSee('Vorname muss ausgefüllt sein')
                ->assertDontSee('Nachname muss ausgefüllt sein')
                ->assertSee('Telefon muss ausgefüllt sein')
                ->assertDontSee('Email')
                ->assertDontSee('Password');

            self::assertEquals($user_count + 1, User::count());
            self::assertEquals($customer_count, Customer::count());

            $browser->type('first_name', $this->faker->firstName())
                ->type('phone', $this->faker->phoneNumber());

            $browser->screenshot('mamiea');

            // Lieferoptionen Testen
            $browser->click('input.delivery-option-match')->pause(80)
                ->assertNotPresent('.billing-address')->assertPresent('.delivery-address');
            $browser->click('input.delivery-option-pickup')->pause(80)
                ->assertPresent('.billing-address')->assertNotPresent('.delivery-address');
            $browser->click('input.delivery-option-split')->pause(80)
                ->assertPresent('.billing-address')->assertPresent('.delivery-address');

            $browser->type('delivery_address_street', $this->faker->streetAddress());
            $browser->type('delivery_address_postcode', Postcode::inRandomOrder()->first()->postcode);
            $browser->type('delivery_address_city', $this->faker->city());

            $browser->click('button#proceed')
                ->assertPathBeginsWith('/bundle/buy')
                ->pause(250)
                ->assertDontSee('Passwort muss ausgefüllt sein.') // Passwort Fehler ist weg
                ->assertDontSee('Vorname muss ausgefüllt sein')
                ->assertDontSee('Nachname muss ausgefüllt sein')
                ->assertDontSee('Telefon muss ausgefüllt sein')
                ->assertSee('Strasse muss ausgefüllt sein');

            $browser->type('billing_address_street', $this->faker->streetAddress());
            $browser->type('billing_address_postcode', $this->faker->postcode());
            $browser->type('billing_address_city', $this->faker->city());

            self::assertTrue(Auth::guest());

            $browser->click('button#proceed')
                ->pause(300)
                ->assertPathBeginsWith('/buy')
                ->assertSee('Besten Dank für Ihre Bestellung');

            self::assertEquals($user_count + 1, User::count());
            self::assertEquals($customer_count + 1, Customer::count());
            self::assertEquals($addresses_count + 2, Address::count());
        });
    }
}
