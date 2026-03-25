<?php

namespace App\Livewire\Admin;

use App\Models\Badge;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Users extends Component
{
    public string $filterRole = '';
    public string $search = '';

    public ?int $editingId = null;
    public string $editingName = '';
    public string $editingEmail = '';

    // Block modal
    public bool $showBlockModal = false;
    public ?int $blockUserId = null;
    public string $blockType = 'account';
    public string $blockDuration = 'indefinite';
    public string $blockReason = '';

    // Badge management modal
    public bool $showBadgeModal = false;
    public ?int $badgeUserId = null;
    public ?int $assignBadgeId = null;

    // Location management modal
    public bool $showLocationModal = false;
    public ?int $locationUserId = null;
    public ?int $assignLocationId = null;

    public bool $showCreateModal = false;
    public string $newName = '';
    public string $newEmail = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public function openCreate(): void
    {
        $this->reset(['newName', 'newEmail', 'newPassword', 'newPasswordConfirmation']);
        $this->showCreateModal = true;
    }

    public function closeCreate(): void
    {
        $this->showCreateModal = false;
        $this->reset(['newName', 'newEmail', 'newPassword', 'newPasswordConfirmation']);
    }

    public function create(): void
    {
        $this->validate([
            'newName' => ['required', 'string', 'max:255'],
            'newEmail' => ['required', 'email', 'max:255', 'unique:users,email'],
            'newPassword' => ['required', 'string', 'min:8', 'same:newPasswordConfirmation'],
            'newPasswordConfirmation' => ['required'],
        ]);

        User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'password' => $this->newPassword,
            'is_admin' => false,
        ]);

        $this->closeCreate();
        session()->flash('status', 'User created successfully.');
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
    }

    public function update(): void
    {
        $this->validate([
            'editingName' => ['required', 'string', 'max:255'],
            'editingEmail' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->editingId],
        ]);

        $user = User::findOrFail($this->editingId);

        $user->update([
            'name' => $this->editingName,
            'email' => $this->editingEmail,
        ]);

        $this->cancelEdit();
        session()->flash('status', 'User updated successfully.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'editingName', 'editingEmail']);
    }

    public function openBlockModal(int $userId, string $type = 'account'): void
    {
        $this->blockUserId = $userId;
        $this->blockType = $type;
        $this->blockDuration = 'indefinite';
        $this->blockReason = '';
        $this->showBlockModal = true;
    }

    public function closeBlockModal(): void
    {
        $this->showBlockModal = false;
        $this->reset(['blockUserId', 'blockType', 'blockDuration', 'blockReason']);
    }

    public function blockUser(): void
    {
        $this->validate([
            'blockReason' => ['required', 'string', 'max:500'],
            'blockDuration' => ['required', 'in:day,indefinite'],
            'blockType' => ['required', 'in:account,comment'],
        ]);

        $user = User::findOrFail($this->blockUserId);

        if ($user->id === Auth::id()) {
            session()->flash('status', 'You cannot block yourself.');
            $this->closeBlockModal();
            return;
        }

        $expiresAt = $this->blockDuration === 'day' ? now()->addDay() : null;

        $reason = strip_tags($this->blockReason);

        if ($this->blockType === 'account') {
            DB::transaction(function () use ($user, $expiresAt, $reason) {
                $user->update([
                    'is_blocked' => true,
                    'blocked_until' => $expiresAt,
                    'block_reason' => $reason,
                    'blocked_by' => Auth::id(),
                ]);
                // Revoke all API tokens
                $user->tokens()->delete();
                // Invalidate all sessions
                DB::table('sessions')->where('user_id', $user->id)->delete();
            });
            $label = $expiresAt ? 'Account blocked for 24 hours.' : 'Account permanently blocked.';
        } else {
            $user->update([
                'is_comment_blocked' => true,
                'comment_blocked_until' => $expiresAt,
                'comment_block_reason' => $reason,
                'comment_blocked_by' => Auth::id(),
            ]);
            $label = $expiresAt ? 'Comments blocked for 24 hours.' : 'Comments permanently blocked.';
        }

        $this->closeBlockModal();
        session()->flash('status', $label);
    }

    public function unblockAccount(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_blocked' => false,
            'blocked_until' => null,
            'block_reason' => null,
            'blocked_by' => null,
        ]);
        session()->flash('status', 'Account unblocked.');
    }

    public function unblockComments(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_comment_blocked' => false,
            'comment_blocked_until' => null,
            'comment_block_reason' => null,
            'comment_blocked_by' => null,
        ]);
        session()->flash('status', 'Comments unblocked.');
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('status', 'You cannot delete yourself.');
            return;
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->delete();

        session()->flash('status', 'User deleted successfully.');
    }

    public function openBadges(int $id): void
    {
        $this->badgeUserId = $id;
        $this->assignBadgeId = null;
        $this->showBadgeModal = true;
    }

    public function closeBadges(): void
    {
        $this->showBadgeModal = false;
        $this->reset(['badgeUserId', 'assignBadgeId']);
    }

    public function assignBadge(): void
    {
        $this->validate([
            'assignBadgeId' => ['required', 'exists:badges,id'],
        ]);

        $user = User::findOrFail($this->badgeUserId);

        $exists = DB::table('badge_user')
            ->where('badge_id', $this->assignBadgeId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            session()->flash('status', 'User already has this badge.');
            $this->assignBadgeId = null;
            return;
        }

        DB::table('badge_user')->insert([
            'badge_id' => $this->assignBadgeId,
            'user_id' => $user->id,
            'count' => 1,
            'collected_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assignBadgeId = null;
        session()->flash('status', 'Badge assigned.');
    }

    public function removeBadge(int $badgeId): void
    {
        DB::table('badge_user')
            ->where('badge_id', $badgeId)
            ->where('user_id', $this->badgeUserId)
            ->delete();

        session()->flash('status', 'Badge removed from user.');
    }

    public function openLocations(int $id): void
    {
        $this->locationUserId = $id;
        $this->assignLocationId = null;
        $this->showLocationModal = true;
    }

    public function closeLocations(): void
    {
        $this->showLocationModal = false;
        $this->reset(['locationUserId', 'assignLocationId']);
    }

    public function assignLocation(): void
    {
        $this->validate([
            'assignLocationId' => ['required', 'exists:locations,id'],
        ]);

        $user = User::findOrFail($this->locationUserId);

        $exists = DB::table('location_user')
            ->where('location_id', $this->assignLocationId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            session()->flash('status', 'User already has this location.');
            $this->assignLocationId = null;
            return;
        }

        DB::table('location_user')->insert([
            'location_id' => $this->assignLocationId,
            'user_id' => $user->id,
            'revealed_at' => now(),
        ]);

        $this->assignLocationId = null;
        session()->flash('status', 'Location assigned.');
    }

    public function removeLocation(int $locationId): void
    {
        DB::table('location_user')
            ->where('location_id', $locationId)
            ->where('user_id', $this->locationUserId)
            ->delete();

        session()->flash('status', 'Location removed from user.');
    }

    public function render()
    {
        $query = User::orderBy('name');

        if ($this->filterRole === 'admin') {
            $query->where('is_admin', true);
        } elseif ($this->filterRole === 'user') {
            $query->where('is_admin', false);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $data = [
            'users' => $query->get(),
        ];

        if ($this->showBadgeModal && $this->badgeUserId) {
            $data['badgeUser'] = User::find($this->badgeUserId);
            $data['userBadges'] = DB::table('badge_user')
                ->join('badges', 'badges.id', '=', 'badge_user.badge_id')
                ->where('badge_user.user_id', $this->badgeUserId)
                ->whereNull('badges.deleted_at')
                ->select('badges.id', 'badges.title', 'badges.image_path', 'badge_user.count', 'badge_user.collected_at')
                ->get();
            $data['allBadges'] = Badge::orderBy('title')->get();
        }

        if ($this->showLocationModal && $this->locationUserId) {
            $data['locationUser'] = User::find($this->locationUserId);
            $data['userLocations'] = DB::table('location_user')
                ->join('locations', 'locations.id', '=', 'location_user.location_id')
                ->where('location_user.user_id', $this->locationUserId)
                ->select('locations.id', 'locations.title', 'locations.image_path', 'location_user.revealed_at')
                ->get();
            $data['allLocations'] = Location::where('is_active', true)->orderBy('title')->get();
        }

        return view('livewire.admin.users', $data)->layout('layouts.admin');
    }
}
