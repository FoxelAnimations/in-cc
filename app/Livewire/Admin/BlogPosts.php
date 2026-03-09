<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BlogPosts extends Component
{
    use WithFileUploads;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $excerpt = '';
    public string $content = '';
    public $featured_image = null;
    public ?string $existing_image_path = null;
    public string $button_label = '';
    public string $button_url = '';
    public bool $button_new_tab = false;
    public bool $is_published = false;
    public bool $is_visible = true;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string', 'max:50000'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'button_label' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'url', 'max:500'],
            'button_new_tab' => ['boolean'],
            'is_published' => ['boolean'],
            'is_visible' => ['boolean'],
        ];
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content ?? '';
        $this->button_label = $post->button_label ?? '';
        $this->button_url = $post->button_url ?? '';
        $this->button_new_tab = $post->button_new_tab;
        $this->is_published = $post->is_published;
        $this->is_visible = $post->is_visible;
        $this->existing_image_path = $post->featured_image;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $cleanContent = $this->content
            ? strip_tags($this->content, '<p><br><strong><em><u><h2><h3><ul><ol><li>')
            : null;
        if ($cleanContent && trim(strip_tags($cleanContent)) === '') {
            $cleanContent = null;
        }

        $data = [
            'title' => $this->title,
            'excerpt' => $this->excerpt ?: null,
            'content' => $cleanContent,
            'button_label' => $this->button_label ?: null,
            'button_url' => $this->button_url ?: null,
            'button_new_tab' => $this->button_new_tab,
            'is_published' => $this->is_published,
            'is_visible' => $this->is_visible,
        ];

        // Handle image
        if ($this->featured_image) {
            if ($this->existing_image_path) {
                Storage::disk('public')->delete($this->existing_image_path);
            }
            $data['featured_image'] = $this->featured_image->store('blog', 'public');
        } elseif ($this->editingId) {
            $data['featured_image'] = $this->existing_image_path;
        }

        if ($this->editingId) {
            $post = BlogPost::findOrFail($this->editingId);
            // Clean up old image if replaced
            if ($post->featured_image && isset($data['featured_image']) && $data['featured_image'] !== $post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['slug'] = BlogPost::generateSlug($this->title, $this->editingId);
            $post->update($data);
            session()->flash('status', 'Blog post bijgewerkt.');
        } else {
            $data['slug'] = BlogPost::generateSlug($this->title);
            $data['sort_order'] = (BlogPost::max('sort_order') ?? -1) + 1;
            $data['published_at'] = $this->is_published ? now() : null;
            BlogPost::create($data);
            session()->flash('status', 'Blog post aangemaakt.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function togglePublished(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $post->update([
            'is_published' => !$post->is_published,
            'published_at' => !$post->is_published ? ($post->published_at ?? now()) : $post->published_at,
        ]);
    }

    public function toggleVisible(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $post->update(['is_visible' => !$post->is_visible]);
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            BlogPost::where('id', $id)->update(['sort_order' => $index]);
        }
    }

    public function delete(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        session()->flash('status', 'Blog post verwijderd.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'excerpt', 'content', 'featured_image',
            'button_label', 'button_url', 'button_new_tab',
            'existing_image_path',
        ]);
        $this->is_published = false;
        $this->is_visible = true;
    }

    public function render()
    {
        return view('livewire.admin.blog-posts', [
            'posts' => BlogPost::orderBy('sort_order')->get(),
        ])->layout('layouts.admin');
    }
}
