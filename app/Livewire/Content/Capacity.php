<?php

namespace App\Livewire\Content;

use Livewire\Component;

class Capacity extends Component
{
    use Traits\WithCapacityPlanning;

    public function mount()
    {
        $this->capacityWeekStart = now()->startOfWeek();
    }

    public function render()
    {
        return view('livewire.content.capacity')
            ->layout('layouts.admin', ['title' => 'Capacity Planning']);
    }
}
