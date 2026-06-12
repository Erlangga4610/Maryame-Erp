<?php

namespace App\Livewire\MasterData;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use HasMasterDataPermissions, WithPagination;

    public $showModal = false;

    public $modalMode = 'create';

    public $editId = null;

    public $showDeleteModal = false;

    public $deleteId = null;

    public $deleteName = '';

    public $sku = '';

    public $name = '';

    public $description = '';

    public $category = '';

    public $is_active = true;

    protected function rules()
    {
        $unique = $this->modalMode === 'create'
            ? 'unique:products,sku'
            : 'unique:products,sku,'.$this->editId;

        return [
            'sku' => 'required|string|max:50|'.$unique,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    protected $validationAttributes = [
        'sku' => 'SKU',
        'name' => 'Nama Produk',
    ];

    public function render()
    {
        return view('livewire.master-data.products', [
            'products' => Product::orderBy('name')->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Products']);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $product = Product::findOrFail($id);
        $this->editId = $product->id;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->category = $product->category;
        $this->is_active = $product->is_active;
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
            Product::create([
                'sku' => strtoupper($this->sku),
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->category,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Produk berhasil ditambahkan!');
        } else {
            $product = Product::findOrFail($this->editId);
            $product->update([
                'sku' => strtoupper($this->sku),
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->category,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Produk berhasil diperbarui!');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $product = Product::findOrFail($id);
        $this->deleteId = $product->id;
        $this->deleteName = $product->name;
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

        Product::findOrFail($this->deleteId)->delete();
        flash()->success('Produk berhasil dihapus!');
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
        $this->sku = '';
        $this->name = '';
        $this->description = '';
        $this->category = '';
        $this->is_active = true;
    }
}
