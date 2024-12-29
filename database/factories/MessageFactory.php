<?php

namespace Database\Factories;

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
        $senderId = $this->faker->randomElement([0, 1]);

        if ($senderId === 0) {
            // Exclude user with ID 1 for sender
            $senderId = $this->faker->randomElement(
                User::where('id', '!=', 1)->pluck('id')->toArray()
            );
            $recieverId = 1;
        } else {
            // Pick any user as reciever
            $recieverId = $this->faker->randomElement(User::pluck('id')->toArray());
        }
        
        $groupId = null;

        // 50% chance to assign a group
        if ($this->faker->boolean(50)) {
            $groupId = $this->faker->randomElement(Group::pluck('id')->toArray());

            // Fetch the group and pick a sender from its users
            $group = Group::find($groupId);

            // Ensure the group has users
            if ($group && $group->users->isNotEmpty()) {
                $senderId = $this->faker->randomElement(
                    $group->users->pluck('id')->toArray()
                );
                $recieverId = null; // No direct reciever for group messages
            }
        }

        return [
            'sender_id' => $senderId,
            'reciever_id' => $recieverId,
            'group_id' => $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
