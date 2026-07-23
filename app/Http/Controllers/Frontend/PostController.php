<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function __invoke(Post $post): View
    {
        abort_unless($post->status === 'published', 404);

        return view('frontend.post', [
            'post'     => $post->load('category'),
            'sections' => $post->renderableSections(),
        ]);
    }
}
