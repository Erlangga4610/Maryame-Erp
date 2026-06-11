<?php

namespace App\MasterData\Livewire;

use App\Models\Campaign;
use Livewire\Component;
use Livewire\WithPagination;

class Campaigns extends Component
{
    use HasMasterDataPermissions, WithPagination;

    public $showModal = false;

    public $modalMode = 'create';

    public $editId = null;

    public $name = '';

    public $type = 'seasonal';

    public $start_date = '';

    public $end_date = '';

    public $objective = '';

    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:seasonal,launching,tactical',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'objective' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nama Campaign',
        'type' => 'Tipe Campaign',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Berakhir',
    ];

    public function render()
    {
        return view('master-data.campaigns', [
            'campaigns' => Campaign::orderBy('start_date', 'desc')->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Campaigns']);
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $campaign = Campaign::findOrFail($id);
        $this->editId = $campaign->id;
        $this->name = $campaign->name;
        $this->type = $campaign->type;
        $this->start_date = $campaign->start_date?->format('Y-m-d');
        $this->end_date = $campaign->end_date?->format('Y-m-d');
        $this->objective = $campaign->objective;
        $this->is_active = $campaign->is_active;
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
            Campaign::create([
                'name' => $this->name,
                'type' => $this->type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'objective' => $this->objective,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Campaign berhasil ditambahkan!');
        } else {
            $campaign = Campaign::findOrFail($this->editId);
            $campaign->update([
                'name' => $this->name,
                'type' => $this->type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'objective' => $this->objective,
                'is_active' => $this->is_active,
            ]);
            flash()->success('Campaign berhasil diperbarui!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        if (! $this->canEdit()) {
            flash()->error('Anda tidak memiliki izin untuk menghapus data.');

            return;
        }

        Campaign::findOrFail($id)->delete();
        flash()->success('Campaign berhasil dihapus!');
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
        $this->type = 'seasonal';
        $this->start_date = '';
        $this->end_date = '';
        $this->objective = '';
        $this->is_active = true;
    }
}
