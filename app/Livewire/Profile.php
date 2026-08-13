<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public $name = '';

    public $email = '';

    public $employee_id = '';

    public $position = '';

    public $department = '';

    public $phone = '';

    public $avatar;

    public $avatarPath = null;

    public $current_password = '';

    public $password = '';

    public $password_confirmation = '';

    protected $validationAttributes = [
        'name' => 'Nama',
        'email' => 'Email',
        'phone' => 'Nomor HP',
        'position' => 'Posisi',
        'department' => 'Departemen',
        'avatar' => 'Foto Profil',
        'current_password' => 'Password saat ini',
        'password' => 'Password baru',
        'password_confirmation' => 'Konfirmasi password',
    ];

    public function mount()
    {
        $this->avatarPath = Auth::user()->avatar_path;
        $this->loadProfile();
    }

    public function render()
    {
        return view('livewire.profile', [
            'roles' => Auth::user()->roles,
            'departments' => ['Creative', 'Marketing', 'Sales', 'Research', 'Legal', 'General'],
            'avatarUrl' => $this->avatarPath ? url('storage/'.$this->avatarPath) : null,
        ])->layout('layouts.admin', ['title' => 'Profil']);
    }

    public function uploadAvatar()
    {
        $this->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $user = User::findOrFail(Auth::id());

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $extension = $this->avatar->getClientOriginalExtension();
        $path = Storage::disk('public')->putFileAs('avatars', $this->avatar, 'avatar-'.$user->id.'.'.$extension);

        $user->update(['avatar_path' => $path]);

        $this->avatarPath = $path;
        flash()->success('Foto profil berhasil diupload!');
        $this->reset('avatar');
    }

    public function removeAvatar()
    {
        $user = User::findOrFail(Auth::id());

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        $this->avatarPath = null;
        $this->reset('avatar');
        flash()->success('Foto profil berhasil dihapus.');
    }

    public function updateProfile()
    {
        $rules = [
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:50',
        ];

        $this->validate($rules);

        $user = User::findOrFail(Auth::id());
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'department' => $this->department,
        ]);

        flash()->success('Profil berhasil diperbarui!');
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::findOrFail(Auth::id());

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');

            return;
        }

        $user->update(['password' => Hash::make($this->password)]);

        flash()->success('Password berhasil diubah!');
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    private function loadProfile()
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id;
        $this->position = $user->position;
        $this->department = $user->department;
        $this->phone = $user->phone;
    }
}
