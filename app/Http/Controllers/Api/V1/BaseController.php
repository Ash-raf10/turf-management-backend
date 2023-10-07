<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * BaseController
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class BaseController extends Controller
{
    use ApiResponse;

    /**
     * transformPaginateData
     *
     * @param  LengthAwarePaginator $results
     *
     * @return array
     */
    protected function transformPaginateData(LengthAwarePaginator $results): array
    {
        $paginateData = [
            'total' => $results->total(),
            'count' => $results->count(),
            'per_page' => $results->perPage(),
            'prv_page' => $results->previousPageUrl(),
            'nxt_page' => $results->nextPageUrl()
        ];
        $itemData = $results->items();

        return [$itemData, $paginateData];
    }

    /**
     * paginateData
     * This will return an array with the dataset
     * and with the pagination information
     *
     * @param  Builder $query
     * @return array < item,pagination >
     */
    protected function paginateData(Builder $query): array
    {
        $results = $query->paginate($this->limit());

        return $this->transformPaginateData($results);
    }

    private function limit()
    {
        return request()->query('limit') ?? 10;
    }
}
