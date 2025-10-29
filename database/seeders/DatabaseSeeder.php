<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User seed
        User::factory()->create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('123456'),
        ]);

        User::factory()->create([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => bcrypt('123456'),
        ]);

        User::factory(10)->create();

        // Group & Group-Conversation-Messages seed
        for ($i = 0; $i < 5; $i++) {
            /**
             * @var Group
             */
            $group = Group::factory()->create(['owner_id' => 1]);

            $users = User::inRandomOrder()->limit(rand(5, 10))->pluck('id');
            $group->users()->attach(array_unique([1, ...$users]));

            $conversation = Conversation::factory()->create([
                'group_id' => $group->id,
            ]);

            for ($j = 0; $j < 100; $j++) {
                Message::factory()->create([
                    'sender_id'       => fake()->randomElement(array_unique([1, ...$users])),
                    'conversation_id' => $conversation->id,
                ]);
            }
        }

        // Seed User-Conversation-Messages
        for ($i = 0; $i < 50; $i++) {
            $senderId = fake()->randomElement([0, 1]);

            if ($senderId === 1) {
                $receiverId = fake()->randomElement(
                    User::where('id', '!=', 1)->pluck('id')->toArray()
                );
            } else {
                $senderId   = fake()->randomElement(
                    User::where('id', '!=', 1)->pluck('id')->toArray()
                );
                $receiverId = 1;
            }

            $conversationId = Message::where(function (Builder $q) use ($receiverId, $senderId) {
                $q->where('receiver_id', '=', $receiverId)
                    ->where('sender_id', '=', $senderId);
            })
                ->orWhere(function (Builder $q) use ($receiverId, $senderId) {
                    $q->where('receiver_id', '=', $senderId)
                        ->where('sender_id', '=', $receiverId);
                })
                ->first()?->conversation_id;

            if (! $conversationId) {
                /**
                 * @var Conversation
                 */
                $newConversation = Conversation::factory()->create();
                $newConversation->users()->attach([$senderId, $receiverId]);

                $conversationId = $newConversation->id;
            }

            for ($j = 0; $j < 10; $j++) {
                $messageSender   = fake()->randomElement([$senderId, $receiverId]);
                $messageReceiver = $messageSender === $senderId ? $receiverId : $senderId;

                Message::factory()->create([
                    'conversation_id' => $conversationId,
                    'sender_id'       => $messageSender,
                    'receiver_id'     => $messageReceiver,
                ]);
            }
        }

        $latestMessages = Message::selectRaw('conversation_id, MAX(created_at) as last_message_date')
            ->groupBy('conversation_id')
            ->get();

        // Step 2: Update the conversations table
        foreach ($latestMessages as $latestMessage) {
            $lastMessageId = Message::where('conversation_id', $latestMessage->conversation_id)
                ->where('created_at', $latestMessage->last_message_date)
                ->value('id');

            Conversation::where('id', $latestMessage->conversation_id)
                ->update(['last_message_id' => $lastMessageId]);
        }
    }
}
