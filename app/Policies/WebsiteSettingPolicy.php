<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebsiteSetting;

class WebsiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage settings');
    }

    public function view(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->can('manage settings');
    }

    public function create(User $user): bool
    {
        return $user->can('manage settings');
    }

    public function update(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->can('manage settings');
    }

    public function delete(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->can('manage settings');
    }

    public function restore(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->can('manage settings');
    }

    public function forceDelete(User $user, WebsiteSetting $websiteSetting): bool
    {
        return $user->can('manage settings');
    }
}
