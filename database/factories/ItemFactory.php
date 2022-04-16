<?php

namespace Database\Factories;

use App\Models\ItemOrigin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement([
                'Apfel', 'Birne', 'Pfirsich', 'Randen', 'Zwiebeln', 'Rhabarber', 'Kartoffel', 'Banane', 'Kaffee',
                'Eier', 'Krautstiel gelb', 'Krautstiel rot', 'Spinat', 'Peperoni Grün', 'Peperoni Gelb',
                'Peperoni Rot', 'Alpkäse', 'Dörraprikosen', 'Walnüsse', 'Holunderblütensirup', 'Tomaten',
                'Cherry Tomaten', 'Kopfsalat', 'Kopfsalat Rot', 'Batavia Grün', 'Nüsslisalat', 'Rüebli',
                'Kirschen', 'Apfel Boskop', 'Honig', 'Himbeeren', 'Erdbeeren', 'Stachelbeeren', 'Johanisbeeren'
            ]),
            'item_origin_id' => ItemOrigin::inRandomOrder()->first()->id
        ];
    }
}
