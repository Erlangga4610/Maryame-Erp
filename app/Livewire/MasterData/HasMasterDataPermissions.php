<?php

namespace App\Livewire\MasterData;

use Illuminate\Support\Facades\Auth;

trait HasMasterDataPermissions
{
    public function canEdit(): bool
    {
        if (Auth::user()?->isSuperAdmin()) {
            return true;
        }

        $tier = Auth::user()?->roles->first()?->rbac_tier;

        return $tier === 1;
    }

    public function canView(): bool
    {
        if (Auth::user()?->isSuperAdmin()) {
            return true;
        }

        return Auth::user()?->roles->first()?->rbac_tier !== null;
    }
}
