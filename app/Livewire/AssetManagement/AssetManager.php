<?php

namespace App\Livewire\AssetManagement;

use App\Content\Models\Asset;
use App\Content\Models\Content;
use App\Content\Models\ContentVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AssetManager extends Component
{
    use WithFileUploads;

    public $contentId;

    public $finalAsset;

    public $thumbnail;

    public $existingFinalAsset = null;

    public $existingThumbnail = null;

    public function mount($contentId)
    {
        $this->contentId = $contentId;
        $content = Content::find($contentId);
        $this->existingFinalAsset = $content?->final_asset_link;
        $this->existingThumbnail = $content?->thumbnail_link;
    }

    public function uploadFinalAsset()
    {
        $this->validate([
            'finalAsset' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi|max:204800',
        ]);

        $ext = $this->finalAsset->getClientOriginalExtension();
        $cleanName = Str::slug(pathinfo($this->finalAsset->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
        $path = $this->finalAsset->storeAs('assets', $cleanName, 'public');

        $content = Content::findOrFail($this->contentId);
        $content->update(['final_asset_link' => $path]);

        Asset::create([
            'content_id' => $this->contentId,
            'type' => 'final',
            'link_or_path' => $path,
            'version' => $content->version,
            'uploaded_by' => Auth::id(),
        ]);

        $this->existingFinalAsset = $path;
        flash()->success('Asset final berhasil diupload!');
        $this->dispatch('asset-updated', contentId: $this->contentId);
    }

    public function uploadThumbnail()
    {
        $this->validate([
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $ext = $this->thumbnail->getClientOriginalExtension();
        $cleanName = Str::slug(pathinfo($this->thumbnail->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
        $path = $this->thumbnail->storeAs('thumbnails', $cleanName, 'public');

        $content = Content::findOrFail($this->contentId);
        $content->update(['thumbnail_link' => $path]);

        Asset::create([
            'content_id' => $this->contentId,
            'type' => 'thumbnail',
            'link_or_path' => $path,
            'version' => $content->version,
            'uploaded_by' => Auth::id(),
        ]);

        $this->existingThumbnail = $path;
        flash()->success('Thumbnail berhasil diupload!');
        $this->dispatch('asset-updated', contentId: $this->contentId);
    }

    public function deleteAsset($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        Storage::disk('public')->delete($asset->link_or_path);
        $asset->delete();

        flash()->success('Asset berhasil dihapus.');
        $this->dispatch('asset-updated', contentId: $this->contentId);
    }

    public function deleteFinalAsset()
    {
        $content = Content::findOrFail($this->contentId);
        if ($content->final_asset_link) {
            Storage::disk('public')->delete($content->final_asset_link);
            $content->update(['final_asset_link' => null]);
            $this->existingFinalAsset = null;
            flash()->success('Asset final berhasil dihapus.');
            $this->dispatch('asset-updated', contentId: $this->contentId);
        }
    }

    public function deleteThumbnail()
    {
        $content = Content::findOrFail($this->contentId);
        if ($content->thumbnail_link) {
            Storage::disk('public')->delete($content->thumbnail_link);
            $content->update(['thumbnail_link' => null]);
            $this->existingThumbnail = null;
            flash()->success('Thumbnail berhasil dihapus.');
            $this->dispatch('asset-updated', contentId: $this->contentId);
        }
    }

    public function render()
    {
        $content = Content::with('platform')->findOrFail($this->contentId);
        $versions = ContentVersion::with('creator')
            ->where('content_id', $this->contentId)
            ->orderBy('version', 'desc')
            ->get();
        $assetList = Asset::where('content_id', $this->contentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.asset-management.asset-manager', [
            'content' => $content,
            'versions' => $versions,
            'assetList' => $assetList,
        ])->layout('layouts.admin', ['title' => 'Assets #'.$content->content_code]);
    }
}
