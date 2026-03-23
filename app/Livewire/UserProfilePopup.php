<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class UserProfilePopup extends Component
{
    public ?int $userId = null;
    public string $profileName = '';
    public int $badgeCount = 0;
    public array $badges = [];

    public bool $isAccountBlocked = false;
    public bool $isCommentBlocked = false;
    public ?string $accountBlockLabel = null;
    public ?string $commentBlockLabel = null;

    public bool $showBlockForm = false;
    public string $blockType = 'account';
    public string $blockDuration = 'indefinite';
    public string $blockReason = '';

    public function loadUser(int $userId): void
    {
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $this->userId = $user->id;
        $this->profileName = $user->name;

        $userBadges = $user->badges()
            ->where('is_active', true)
            ->orderByPivot('collected_at', 'desc')
            ->get();

        $this->badgeCount = $userBadges->count();
        $this->badges = $userBadges->map(fn ($b) => [
            'title' => $b->title,
            'image' => $b->image_path ? Storage::url($b->image_path) : null,
        ])->toArray();

        $this->isAccountBlocked = $user->isAccountBlocked();
        $this->isCommentBlocked = $user->isCommentBlocked();
        $this->accountBlockLabel = $user->accountBlockLabel();
        $this->commentBlockLabel = $user->commentBlockLabel();

        $this->showBlockForm = false;
        $this->blockReason = '';

        $this->dispatch('user-profile-loaded');
    }

    public function blockUser(): void
    {
        if (! Auth::user()?->is_admin) {
            return;
        }

        $this->validate([
            'blockReason' => ['required', 'string', 'max:500'],
            'blockDuration' => ['required', 'in:day,indefinite'],
            'blockType' => ['required', 'in:account,comment'],
        ]);

        $user = User::findOrFail($this->userId);

        if ($user->id === Auth::id()) {
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
                $user->tokens()->delete();
                DB::table('sessions')->where('user_id', $user->id)->delete();
            });
        } else {
            $user->update([
                'is_comment_blocked' => true,
                'comment_blocked_until' => $expiresAt,
                'comment_block_reason' => $reason,
                'comment_blocked_by' => Auth::id(),
            ]);
        }

        $this->loadUser($user->id);
    }

    public function unblockAccount(): void
    {
        if (! Auth::user()?->is_admin) {
            return;
        }

        $user = User::findOrFail($this->userId);
        $user->update([
            'is_blocked' => false,
            'blocked_until' => null,
            'block_reason' => null,
            'blocked_by' => null,
        ]);

        $this->loadUser($user->id);
    }

    public function unblockComments(): void
    {
        if (! Auth::user()?->is_admin) {
            return;
        }

        $user = User::findOrFail($this->userId);
        $user->update([
            'is_comment_blocked' => false,
            'comment_blocked_until' => null,
            'comment_block_reason' => null,
            'comment_blocked_by' => null,
        ]);

        $this->loadUser($user->id);
    }

    public function render()
    {
        return view('livewire.user-profile-popup');
    }
}
