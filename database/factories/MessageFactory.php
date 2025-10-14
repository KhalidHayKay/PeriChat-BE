<?php

namespace Database\Factories;

use App\Enums\ConversationTypeEnum;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message'    => $this->faker->realText(150),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
        ];

    }
}
