<?php

namespace App\Models\Back\Settings;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Application
{

    /**
     * @var Request
     */
    protected $request;


    /**
     * @param $data
     *
     * @return \stdClass[]
     */
    public static function setAdminIndexData($data): array
    {
        $response = [
            'basic' => new \stdClass(),
            'google_maps_key' => new \stdClass(),
            'cache' => 0
        ];

        if ($data->where('key', 'basic')->first()) {
            $response['basic'] = json_decode($data->where('key', 'basic')->first()->value)[0];
        }
        if ($data->where('key', 'google.maps')->first()) {
            $response['google_maps_key'] = json_decode($data->where('key', 'google.maps')->first()->value)[0];
        }
        if ($data->where('key', 'cache')->first()) {
            $response['cache'] = $data->where('key', 'cache')->first()->value;
        }

        return $response;
    }


    /**
     * @return mixed
     */
    public static function getSystemData()
    {
        return Cache::rememberForever('app', function () {
            return Settings::getList('app', '%', false)->toArray();
        });
    }


    /**
     * @return mixed
     */
    public static function flushSystemData()
    {
        Cache::forget('app');

        return self::getSystemData();
    }

}
