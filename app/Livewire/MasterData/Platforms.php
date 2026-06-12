<?php

namespace App\Livewire\MasterData;

use App\Models\Platform;
use Livewire\Component;
use Livewire\WithPagination;

class Platforms extends Component
{
    use HasMasterDataPermissions, WithPagination;

    public $showModal = false;

    public $modalMode = 'create';

    public $editId = null;

    public $showDeleteModal = false;

    public $deleteId = null;

    public $deleteName = '';

    public $name = '';

    public $code = '';

    public $originality_strict = false;

    public $is_active = true;

    protected function rules()
    {
        $unique = $this->modalMode === 'create'
            ? 'unique:platforms,code'
            : 'unique:platforms,code,'.$this->editId;

        return [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:5|'.$unique,
            'originality_strict' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nama Platform',
        'code' => 'Kode Platform',
    ];

    public function render()
    {
        return view('livewire.master-data.platforms', [
            'platforms' => Platform::orderBy('name')->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Platforms']);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $platform = Platform::findOrFail($id);
        $this->editId = $platform->id;
        $this->name = $platform->name;
        $this->code = $platform->code;
        $this->originality_strict = $platform->originality_strict;
        $this->is_active = $platform->is_active;
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        if (! $this->canEdit()) {
            flash()->error('Anda tidak memiliki izin untuk mengubah data.');

            return;
        }

        $this->validate();

        if ($this->modalMode === 'create') {
            Platform::create([
                'name' => $this->name,
                'code' => strtoupper($this->code),
                'originality_strict' => $this->originality_strict,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Platform berhasil ditambahkan!');
        } else {
            $platform = Platform::findOrFail($this->editId);
            $platform->update([
                'name' => $this->name,
                'code' => strtoupper($this->code),
                'originality_strict' => $this->originality_strict,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Platform berhasil diperbarui!');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $platform = Platform::findOrFail($id);
        $this->deleteId = $platform->id;
        $this->deleteName = $platform->name;
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

        Platform::findOrFail($this->deleteId)->delete();
        flash()->success('Platform berhasil dihapus!');
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
        $this->code = '';
        $this->originality_strict = false;
        $this->is_active = true;
    }
}
