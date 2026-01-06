<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingUserStatFactory extends Factory
{
    public function definition(): array
    {
        $totalCalls = fake()->numberBetween(0, 50);
        $answeredCalls = fake()->numberBetween(0, $totalCalls);
        $voicemailCalls = fake()->numberBetween(0, max(0, $totalCalls - $answeredCalls));
        $noAnswerCalls = max(0, $totalCalls - $answeredCalls - $voicemailCalls);
        $busyCalls = fake()->numberBetween(0, 5);
        $failedCalls = fake()->numberBetween(0, 3);
        $otherCalls = max(0, $totalCalls - $answeredCalls - $voicemailCalls - $noAnswerCalls - $busyCalls - $failedCalls);

        return [
            'user_id' => User::factory(),
            'stats_date' => fake()->date(),
            'total_calls' => $totalCalls,
            'answered_calls' => $answeredCalls,
            'voicemail_calls' => $voicemailCalls,
            'no_answer_calls' => $noAnswerCalls,
            'busy_calls' => $busyCalls,
            'failed_calls' => $failedCalls,
            'other_calls' => $otherCalls,
            'booked_meetings_count' => fake()->numberBetween(0, 10),
            'total_duration' => fake()->numberBetween(0, 7200),
        ];
    }
}
