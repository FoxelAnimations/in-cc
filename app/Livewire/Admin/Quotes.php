<?php

namespace App\Livewire\Admin;

use App\Models\Character;
use App\Models\Quote;
use Livewire\Component;

class Quotes extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $text = '';
    public $character_id = null;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:1000'],
            'character_id' => ['required', 'exists:characters,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $quote = Quote::findOrFail($id);
        $this->editingId = $quote->id;
        $this->text = $quote->text;
        $this->character_id = $quote->character_id;
        $this->is_active = $quote->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'text' => $this->text,
            'character_id' => $this->character_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Quote::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Quote bijgewerkt.');
        } else {
            Quote::create($data);
            session()->flash('status', 'Quote aangemaakt.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $quote = Quote::findOrFail($id);
        $quote->update(['is_active' => !$quote->is_active]);
    }

    public function delete(int $id): void
    {
        Quote::findOrFail($id)->delete();
        session()->flash('status', 'Quote verwijderd.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'text', 'character_id']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.quotes', [
            'quotes' => Quote::with('character')->latest()->get(),
            'characters' => Character::orderBy('first_name')->get(),
        ])->layout('layouts.admin');
    }
}
