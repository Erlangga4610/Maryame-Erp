<?php

namespace App\MasterData\Livewire;

use Illuminate\Support\Facades\Auth;

trait HasMasterDataPermissions
{
    public function canEdit(): bool
    {
        $tier = Auth::user()?->roles->first()?->rbac_tier;

        return $tier === 1;
    }

    public function canView(): bool
    {
        return Auth::user()?->roles->first()?->rbac_tier !== null;
    }
}
