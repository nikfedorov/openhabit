<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        // extract the domain from the app URL to create a realistic admin email
        $url = Config::string('app.url');
        $domain = parse_url($url, PHP_URL_HOST) ?: 'example.com';

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@'.$domain,
            'is_admin' => true,
        ]);
    }
}
