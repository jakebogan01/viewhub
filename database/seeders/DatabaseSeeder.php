<?php

namespace Database\Seeders;

use App\Models\Comment;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $tags = ['ui', 'enhancement', 'bug', 'feature', 'marketing', 'sales'];
        $statuses = ['pending', 'in progress', 'live'];

        User::factory()->create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => bcrypt(123456789),
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
    }
}
