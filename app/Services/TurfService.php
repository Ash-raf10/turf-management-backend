<?php

namespace App\Services;

use App\Models\Turf;
use Illuminate\Database\Eloquent\Builder;

class TurfService
{

    /**
     * get turf list
     * super admin will see all the turfs
     * company can only see their turf
     *
     * @return Builder
     */
    public function getTurfList(): Builder
    {
        $authUser = auth()->user();

        $query = Turf::query()->with('company', 'creator', 'updator');

        if ($authUser->hasRole('admin')) {
            return $query;
        }

        return $query->where('company_id', $authUser->company_id);
    }

    /**
     * saveTurfInfo
     *
     * @param  array $requestData
     * @return Turf
     */
    public function saveTurfInfo(array $requestData): Turf
    {
        if (!isset($requestData['company_id'])) {
            $requestData['company_id'] = auth()->user()->company_id;
        }

        return Turf::create($requestData);
    }
}
