<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Livewire\Component;

class BlogShow extends Component
{
    public BlogPost $post;

    public function mount(string $slug): void
    {
        $this->post = BlogPost::with('episode')
            ->where('slug', $slug)
            ->where('is_visible', true)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.blog-show')
            ->layoutData(['bgClass' => 'bg-black']);
    }
}
