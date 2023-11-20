<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Reply;
use Exception;
use App\Models\TaskLikes;
use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $tags = ['Bug', 'Improvement', 'Issue', 'New Feature', 'UI', 'Research', 'Customer Based'];
        $statuses = ['marketing', 'sales', 'commercial', 'consumer', 'graphics', 'operations', 'it', 'finance', 'legal', 'officers'];

        User::factory()->create([
            'name' => 'Jake Bogan',
            'email' => 'jakebogan01@gmail.com',
            'username' => 'jake',
            'password' => bcrypt(123456789),
            'dark_mode' => false,
            'is_admin' => true,
            'timezone' => 'America/New_York',
        ]);

        foreach ($statuses as $status) {
            Status::factory()->create(['name' => $status]);
        }

        foreach ($tags as $tag) {
            Tag::factory()->create(['name' => $tag]);
        }

        Task::factory(30)->create();
        TaskLikes::factory(30)->create();
        Comment::factory(30)->create();
        Reply::factory(30)->create();
    }
}
