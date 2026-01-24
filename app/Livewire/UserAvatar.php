<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserAvatar extends Component
{
    use WithFileUploads;

    public $user;
    public $avatarFile;
    public $avatarPreview;

    public function mount($user)
    {
        $this->user = $user;
        $this->avatarPreview = $this->user->getPhoto()?->getImageUrl(300, 300) ?? asset('assets/img/team/15.webp');
    }

    public function updatedAvatarFile()
    {
        if ($this->avatarFile) {
            $this->saveAvatarDirect(); // ✅ ENREGISTRE DIRECT
        }
    }

    public function saveAvatarDirect()
    {
        $this->validate([
            'avatarFile' => 'image|max:2048'
        ]);

        // Supprime ancien
        if ($this->user->getPhoto()) {
            Storage::disk('public')->delete($this->user->getPhoto()->filename);
            $this->user->photos()->delete();
        }

        $this->user->attachfiles([$this->avatarFile]);

        $this->avatarFile = null; // Reset input
    }

    public function render()
    {
        return view('livewire.user-avatar');
    }
}
