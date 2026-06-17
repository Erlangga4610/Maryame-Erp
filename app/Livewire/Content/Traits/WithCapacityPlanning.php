<?php

namespace App\Livewire\Content\Traits;

use App\Content\Models\Content;
use App\Models\User;
use App\Models\UserCapacitySetting;
use Illuminate\Support\Facades\Auth;

trait WithCapacityPlanning
{
    public $capacityWeekStart = null;

    public $showCapacityEditModal = false;

    public $capacityEditUserId = null;

    public $capacityEditUserName = '';

    public $capacityEditMaxHours = 40;

    public $capacityEditContents = [];

    public $capacityResolutionNotes = '';

    public $capacityResolutionStep = 0;

    public $confirmByUserId = null;

    public function capacityGoToToday()
    {
        $this->capacityWeekStart = now()->startOfWeek();
    }

    public function capacityPreviousWeek()
    {
        $this->capacityWeekStart = $this->capacityWeekStart->copy()->subWeek();
    }

    public function capacityNextWeek()
    {
        $this->capacityWeekStart = $this->capacityWeekStart->copy()->addWeek();
    }

    public function getCapacityWeekEndProperty()
    {
        return $this->capacityWeekStart->copy()->endOfWeek();
    }

    public function getCapacityDataProperty()
    {
        $start = $this->capacityWeekStart;
        $end = $start->copy()->endOfWeek();

        $contents = Content::with(['picCopy', 'picVisual', 'picVideo'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('deadline_produksi', [$start, $end])
                    ->orWhereBetween('publish_date', [$start, $end]);
            })
            ->get();

        $users = [];

        foreach ($contents as $c) {
            if ($c->est_copy_hours && $c->pic_copy_id) {
                $users[$c->pic_copy_id]['name'] = $c->picCopy->name;
                $users[$c->pic_copy_id]['role'] = 'Copy';
                $users[$c->pic_copy_id]['total'] = ($users[$c->pic_copy_id]['total'] ?? 0) + (float) $c->est_copy_hours;
                $users[$c->pic_copy_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_copy_hours,
                ];
            }
            if ($c->est_visual_hours && $c->pic_visual_id) {
                $users[$c->pic_visual_id]['name'] = $c->picVisual->name;
                $users[$c->pic_visual_id]['role'] = 'Visual';
                $users[$c->pic_visual_id]['total'] = ($users[$c->pic_visual_id]['total'] ?? 0) + (float) $c->est_visual_hours;
                $users[$c->pic_visual_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_visual_hours,
                ];
            }
            if ($c->est_video_hours && $c->pic_video_id) {
                $users[$c->pic_video_id]['name'] = $c->picVideo->name;
                $users[$c->pic_video_id]['role'] = 'Video';
                $users[$c->pic_video_id]['total'] = ($users[$c->pic_video_id]['total'] ?? 0) + (float) $c->est_video_hours;
                $users[$c->pic_video_id]['contents'][] = [
                    'id' => $c->id,
                    'code' => $c->content_code,
                    'theme' => $c->theme,
                    'hours' => (float) $c->est_video_hours,
                ];
            }
        }

        foreach ($users as $id => &$row) {
            $setting = UserCapacitySetting::where('user_id', $id)
                ->where('effective_from', '<=', $start)
                ->orderBy('effective_from', 'desc')
                ->with('confirmer')
                ->first();

            $row['max'] = $setting ? (float) $setting->max_hours : 40;
            $row['total'] = $row['total'] ?? 0;
            $row['contents'] = $row['contents'] ?? [];
            $row['resolution_step'] = $setting?->resolution_step ?? 0;
            $row['resolution_notes'] = $setting?->resolution_notes;
            $row['confirmed_by'] = $setting?->confirmer?->name;
            $row['confirmed_at'] = $setting?->confirmed_at;
            $row['setting_id'] = $setting?->id;
        }

        return $users;
    }

    public function openCapacityEdit($userId)
    {
        $this->capacityEditUserId = $userId;
        $user = User::find($userId);
        $this->capacityEditUserName = $user?->name ?? 'User';

        $setting = UserCapacitySetting::where('user_id', $userId)
            ->where('effective_from', '<=', $this->capacityWeekStart)
            ->orderBy('effective_from', 'desc')
            ->first();

        $this->capacityEditMaxHours = $setting ? (float) $setting->max_hours : 40;

        $this->capacityEditContents = collect($this->capacity_data)
            ->get($userId, [])['contents'] ?? [];

        $this->showCapacityEditModal = true;
    }

    public function closeCapacityEditModal()
    {
        $this->showCapacityEditModal = false;
        $this->capacityEditUserId = null;
    }

    public function saveCapacitySettings()
    {
        $this->validate([
            'capacityEditMaxHours' => 'required|numeric|min:1|max:168',
        ]);

        UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $this->capacityEditUserId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'max_hours' => $this->capacityEditMaxHours,
            ],
        );

        flash()->success('Kapasitas berhasil disimpan.');
        $this->closeCapacityEditModal();
    }

    public function saveCapacityResolution($userId)
    {
        $this->validate([
            'capacityResolutionStep' => 'required|integer|min:1|max:5',
            'capacityResolutionNotes' => 'required|string|max:1000',
        ]);

        UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $userId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'resolution_step' => $this->capacityResolutionStep,
                'resolution_notes' => $this->capacityResolutionNotes,
            ],
        );

        $this->reset('capacityResolutionNotes', 'capacityResolutionStep');
        flash()->success('Tindakan kapasitas berhasil dicatat.');
    }

    public function confirmCapacity($userId)
    {
        UserCapacitySetting::updateOrCreate(
            [
                'user_id' => $userId,
                'effective_from' => $this->capacityWeekStart,
            ],
            [
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
            ],
        );

        flash()->success('Kapasitas sudah dikonfirmasi.');
    }
}
