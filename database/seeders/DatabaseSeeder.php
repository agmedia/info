<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoleAbilitySeeder::class,
            CustomerGroupSeeder::class,
            UserSedder::class,
            SettingsLocalSeeder::class,
            SystemSettingsSeeder::class,
            CategorySeeder::class,
            BlogPostSeeder::class,
            InfoPageSeeder::class,
            FaqSeeder::class,
            ContentBlockSeeder::class,
            ContentBlockSlotSeeder::class,
        ]);

        $seedDummyData = filter_var((string) env('SEED_DUMMY_DATA', 'false'), FILTER_VALIDATE_BOOL);

        if (! $seedDummyData && $this->command) {
            $seedDummyData = $this->command->confirm(
                'Seed extended dummy content dataset (users, blog posts, info pages, FAQs)?',
                false
            );
        }

        if ($seedDummyData) {
            $this->call([
                DummyContentSeeder::class,
            ]);
        }
    }
}
