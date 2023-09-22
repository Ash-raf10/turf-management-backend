<?php

namespace App\Traits;


trait TransformPaginate
{

    /**
     * transformPaginateData
     *
     * @param  LengthAwarePaginator $results
     *
     * @return array
     */
    protected function transformPaginateData($results): array
    {
        $data = $results->items();
        $pagination =  [
            'total' => $results->total(),
            'count' => $results->count(),
            'per_page' => $results->perPage(),
            'prv_page' => $results->previousPageUrl(),
            'nxt_page' => $results->nextPageUrl()
        ];

        return [$data, $pagination];
    }

    protected function paginateData($query)
    {
        return $query->paginate();
    }
}
