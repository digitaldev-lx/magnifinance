<?php

namespace App\Helper;

use App\UniversalSearch;
use Illuminate\Support\Arr;

class SearchLog
{

    public static function createSearchEntry($searchableId, $type, $title, $route, $companyId = null)
    {
        $search = new UniversalSearch();

        $search->searchable_id = mb_convert_encoding($searchableId, 'UTF-8', 'UTF-8');
        $search->searchable_type = mb_convert_encoding($type, 'UTF-8', 'UTF-8');
        $search->title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
        $search->route_name = mb_convert_encoding($route, 'UTF-8', 'UTF-8');
        $search->company_id = mb_convert_encoding($companyId, 'UTF-8', 'UTF-8');

        $search->save();
    }

    public static function updateSearchEntry($searchableId, $type, $title, $route, $data = null)
    {
        $search = UniversalSearch::where(['searchable_id' => $searchableId, 'route_name' => $route]);

        if ($data && !Arr::has($data, 'modified')) {
            $search = $search->where('title', current($data));
        }

        $search = $search->first();

        if($search != null)
        {
            $search->searchable_id = mb_convert_encoding($searchableId, 'UTF-8', 'UTF-8');
            $search->searchable_type = mb_convert_encoding($type, 'UTF-8', 'UTF-8');
            $search->title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
            $search->route_name = mb_convert_encoding($route, 'UTF-8', 'UTF-8');

            if ($data && Arr::has($data, ['modified'])) {
                $value = $data['modified'];

                $search->searchable_id = mb_convert_encoding($value['searchable_id'], 'UTF-8', 'UTF-8');
                $search->title = mb_convert_encoding($value['title'], 'UTF-8', 'UTF-8');
            }

            $search->save();
        }

    }

    public static function deleteSearchEntry($searchableId, $route)
    {
        $searches = UniversalSearch::where(['searchable_id' => $searchableId, 'route_name' => $route])->get();

        foreach ($searches as $search) {
            $search->delete();
        }
    }

}
