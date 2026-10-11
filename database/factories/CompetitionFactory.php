<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Competition\Enums\CompetitionStatus;
use App\Domain\Competition\Enums\DrawMode;
use App\Domain\Competition\Enums\NoWinnerPolicy;
use App\Domain\Competition\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->sentence(),
            'status' => CompetitionStatus::Draft,
            'ticket_price' => fake()->numberBetween(1, 50) * 1000,
            'cashier_commission_fixed' => 1000,
            'master_commission_fixed' => 500,
            'prize_amount' => fake()->numberBetween(100, 5000) * 1000,
            'draw_mode' => DrawMode::Random,
            'no_winner_policy' => NoWinnerPolicy::NoWinner,
            'draw_datetime' => now()->addDays(7),
            'sales_open_datetime' => now()->subDay(),
            'sales_close_datetime' => now()->addDays(6),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => CompetitionStatus::Active]);
    }
}
