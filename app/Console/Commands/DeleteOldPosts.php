<?php

namespace App\Console\Commands;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldPosts extends Command
{
    protected $signature = 'app:delete-old-posts';

    protected $description = 'Command description';

    public function handle()
    {
        $posts = Post::limit(10)->get();

        if ($posts->isEmpty()) {
            $this->info('Unable to find post');
            return;
        }

        $deletedCount = 0;
        foreach ($posts as $post) {
            $post->delete();
            $deletedCount++;
        }

        $this->info("Deleted $deletedCount old posts.");
        return;
    }
}
