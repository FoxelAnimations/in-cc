<?php

namespace App\Livewire\Admin;

use App\Models\Character;
use App\Models\Collab;
use App\Models\Episode;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Collabs extends Component
{
    use WithFileUploads;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $content = '';
    public $featured_image = null;
    public ?string $existing_image_path = null;
    public $logo_image = null;
    public ?string $existing_logo_path = null;
    public $episode_id = null;
    public $character_id = null;
    public string $link1_label = '';
    public string $link1_url = '';
    public bool $link1_new_tab = false;
    public string $link2_label = '';
    public string $link2_url = '';
    public bool $link2_new_tab = false;
    public bool $show_on_homepage = false;
    public bool $is_published = false;
    public bool $is_visible = true;

    // Character selector popup
    public bool $showCharacterSelector = false;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'logo_image' => ['nullable', 'image', 'max:4096'],
            'episode_id' => ['nullable', 'exists:episodes,id'],
            'character_id' => ['nullable', 'exists:characters,id'],
            'link1_label' => ['nullable', 'string', 'max:255'],
            'link1_url' => ['nullable', 'url', 'max:500'],
            'link1_new_tab' => ['boolean'],
            'link2_label' => ['nullable', 'string', 'max:255'],
            'link2_url' => ['nullable', 'url', 'max:500'],
            'link2_new_tab' => ['boolean'],
            'show_on_homepage' => ['boolean'],
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
        $collab = Collab::findOrFail($id);
        $this->editingId = $collab->id;
        $this->title = $collab->title;
        $this->content = $collab->content ?? '';
        $this->episode_id = $collab->episode_id;
        $this->character_id = $collab->character_id;
        $this->link1_label = $collab->link1_label ?? '';
        $this->link1_url = $collab->link1_url ?? '';
        $this->link1_new_tab = $collab->link1_new_tab;
        $this->link2_label = $collab->link2_label ?? '';
        $this->link2_url = $collab->link2_url ?? '';
        $this->link2_new_tab = $collab->link2_new_tab;
        $this->show_on_homepage = $collab->show_on_homepage;
        $this->is_published = $collab->is_published;
        $this->is_visible = $collab->is_visible;
        $this->existing_image_path = $collab->featured_image;
        $this->existing_logo_path = $collab->logo_image;
        $this->showForm = true;
    }

    public function selectCharacter(?int $id): void
    {
        $this->character_id = $id;
        $this->showCharacterSelector = false;
    }

    public function save(): void
    {
        $this->validate();

        $cleanContent = $this->content
            ? strip_tags($this->content, '<p><br><strong><em><u><h2><h3><ul><ol><li><a>')
            : null;
        if ($cleanContent && trim(strip_tags($cleanContent)) === '') {
            $cleanContent = null;
        }

        $data = [
            'title' => $this->title,
            'content' => $cleanContent,
            'episode_id' => $this->episode_id ?: null,
            'character_id' => $this->character_id ?: null,
            'link1_label' => $this->link1_label ?: null,
            'link1_url' => $this->link1_url ?: null,
            'link1_new_tab' => $this->link1_new_tab,
            'link2_label' => $this->link2_label ?: null,
            'link2_url' => $this->link2_url ?: null,
            'link2_new_tab' => $this->link2_new_tab,
            'show_on_homepage' => $this->show_on_homepage,
            'is_published' => $this->is_published,
            'is_visible' => $this->is_visible,
        ];

        // Handle featured image
        if ($this->featured_image) {
            if ($this->existing_image_path) {
                Storage::disk('public')->delete($this->existing_image_path);
            }
            $data['featured_image'] = $this->featured_image->store('collabs', 'public');
        } elseif ($this->editingId) {
            $data['featured_image'] = $this->existing_image_path;
        }

        // Handle logo image
        if ($this->logo_image) {
            if ($this->existing_logo_path) {
                Storage::disk('public')->delete($this->existing_logo_path);
            }
            $data['logo_image'] = $this->logo_image->store('collabs/logos', 'public');
        } elseif ($this->editingId) {
            $data['logo_image'] = $this->existing_logo_path;
        }

        if ($this->editingId) {
            $collab = Collab::findOrFail($this->editingId);
            if ($collab->featured_image && isset($data['featured_image']) && $data['featured_image'] !== $collab->featured_image) {
                Storage::disk('public')->delete($collab->featured_image);
            }
            if ($collab->logo_image && isset($data['logo_image']) && $data['logo_image'] !== $collab->logo_image) {
                Storage::disk('public')->delete($collab->logo_image);
            }
            $data['slug'] = Collab::generateSlug($this->title, $this->editingId);
            $collab->update($data);
            session()->flash('status', 'Collab bijgewerkt.');
        } else {
            $data['slug'] = Collab::generateSlug($this->title);
            $data['sort_order'] = (Collab::max('sort_order') ?? -1) + 1;
            $data['published_at'] = $this->is_published ? now() : null;
            Collab::create($data);
            session()->flash('status', 'Collab aangemaakt.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function togglePublished(int $id): void
    {
        $collab = Collab::findOrFail($id);
        $collab->update([
            'is_published' => !$collab->is_published,
            'published_at' => !$collab->is_published ? ($collab->published_at ?? now()) : $collab->published_at,
        ]);
    }

    public function toggleVisible(int $id): void
    {
        $collab = Collab::findOrFail($id);
        $collab->update(['is_visible' => !$collab->is_visible]);
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Collab::where('id', $id)->update(['sort_order' => $index]);
        }
    }

    public function delete(int $id): void
    {
        $collab = Collab::findOrFail($id);
        if ($collab->featured_image) {
            Storage::disk('public')->delete($collab->featured_image);
        }
        if ($collab->logo_image) {
            Storage::disk('public')->delete($collab->logo_image);
        }
        $collab->delete();
        session()->flash('status', 'Collab verwijderd.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'content', 'featured_image', 'logo_image',
            'episode_id', 'character_id',
            'link1_label', 'link1_url', 'link1_new_tab',
            'link2_label', 'link2_url', 'link2_new_tab',
            'existing_image_path', 'existing_logo_path',
            'showCharacterSelector',
        ]);
        $this->show_on_homepage = false;
        $this->is_published = false;
        $this->is_visible = true;
    }

    public function render()
    {
        return view('livewire.admin.collabs', [
            'collabs' => Collab::with(['episode', 'character'])->orderBy('sort_order')->get(),
            'episodes' => Episode::where('visible', true)->orderBy('title')->get(),
            'characters' => Character::orderBy('sort_order')->get(),
        ])->layout('layouts.admin');
    }
}
