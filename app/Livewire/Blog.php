<?php

namespace App\Livewire;

use App\Models\BlogPost;
use App\Models\ContentBlock;
use Livewire\Component;

class Blog extends Component
{
    public function render()
    {
        return view('livewire.blog', [
            'contentBlocks' => ContentBlock::active()->forBlog()->get(),
            'posts' => BlogPost::published()->get(),
        ])->layoutData(['bgClass' => 'bg-black']);
    }
}
