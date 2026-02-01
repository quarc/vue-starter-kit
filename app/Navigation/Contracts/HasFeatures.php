<?php

namespace App\Navigation\Contracts;

use Laravel\Pennant\Feature;

trait HasFeatures
{
    /**
     * Check if the feature flag is active
     */
    protected function checkFeature(?string $feature): bool
    {
        if (empty($feature)) {
            return true;
        }

        return Feature::for(auth()->user())->active($feature);
    }
}
