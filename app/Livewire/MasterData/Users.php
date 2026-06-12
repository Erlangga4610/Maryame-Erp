<?php

namespace App\Livewire\MasterData;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Users extends Component
{
    use HasMasterDataPermissions, WithPagination;

    public $showModal = false;

    public $modalMode = 'create';

    public $editId = null;

    public $showDeleteModal = false;

    public $deleteId = null;

    public $deleteName = '';

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $employee_id = '';

    public $position = '';

    public $department = '';

    public $is_active = true;

    public $role = '';

    protected $validationAttributes = [
        'name' => 'Nama',
        'email' => 'Email',
        'employee_id' => 'Employee ID',
        'position' => 'Posisi',
        'department' => 'Departemen',
        'is_active' => 'Status Aktif',
        'role' => 'Role',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
    ];

    public function render()
    {
        return view('livewire.master-data.users', [
            'users' => User::with('roles')->orderBy('name')->paginate(20),
            'roles' => Role::orderBy('name')->get(),
            'departments' => ['Creative', 'Marketing', 'Sales', 'Research', 'Legal', 'General'],
        ])->layout('layouts.admin', ['title' => 'Users']);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id;
        $this->position = $user->position;
        $this->department = $user->department;
        $this->is_active = $user->is_active;
        $this->role = $user->roles->first()?->name ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        if (! $this->canEdit()) {
            flash()->error('Anda tidak memiliki izin untuk mengubah data.');

            return;
        }

        $rules = [
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,'.$this->editId,
            'employee_id' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'role' => 'required|exists:roles,name',
        ];

        if ($this->modalMode === 'create' || $this->password) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'employee_id' => $this->employee_id,
            'position' => $this->position,
            'department' => $this->department,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->modalMode === 'create') {
            $user = User::create($data);
            $user->syncRoles([$this->role]);
            flash()->success('User berhasil ditambahkan!');
        } else {
            $user = User::findOrFail($this->editId);
            $user->update($data);
            $user->syncRoles([$this->role]);
            flash()->success('User berhasil diperbarui!');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        if ((int) $id === (int) Auth::id()) {
            flash()->error('Tidak bisa menghapus akun sendiri!');

            return;
        }

        $user = User::findOrFail($id);
        $this->deleteId = $user->id;
        $this->deleteName = $user->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteName = '';
    }

    public function executeDelete()
    {
        if (! $this->canEdit()) {
            flash()->error('Anda tidak memiliki izin untuk menghapus data.');

            return;
        }

        User::findOrFail($this->deleteId)->delete();
        flash()->success('User berhasil dihapus!');
        $this->cancelDelete();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->employee_id = '';
        $this->position = '';
        $this->department = '';
        $this->is_active = true;
        $this->role = '';
    }
}
