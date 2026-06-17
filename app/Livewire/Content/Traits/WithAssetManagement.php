<?php

namespace App\Livewire\Content\Traits;

use App\Content\Models\ContentVersion;
use Illuminate\Support\Facades\Auth;

trait WithAssetManagement
{
    public $showVersionModal = false;

    public $versionContentId = null;

    public $existingFinalAsset = null;

    public $existingThumbnail = null;

    public $finalAsset = null;

    public $thumbnail = null;

    public function openVersionModal($id)
    {
        $this->versionContentId = $id;
        $this->showVersionModal = true;
    }

    public function closeVersionModal()
    {
        $this->showVersionModal = false;
        $this->versionContentId = null;
    }

    public function archiveVersion($versionId)
    {
        $version = ContentVersion::findOrFail($versionId);
        $version->update([
            'is_archived' => true,
            'archived_by' => Auth::id(),
            'archived_at' => now(),
        ]);

        flash()->success('Versi berhasil diarsipkan.');
    }

    public function getVersionHistoryProperty()
    {
        if (! $this->versionContentId) {
            return collect();
        }

        return ContentVersion::with('creator', 'archiver')
            ->where('content_id', $this->versionContentId)
            ->orderBy('version', 'desc')
            ->get();
    }
}
