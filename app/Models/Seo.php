<?php

namespace App\Models;

use App\Helpers\Metatags;
use Illuminate\Http\Request;

/**
 * Class Sitemap
 * @package App\Models
 */
class Seo
{

    /**
     * @param Request $request
     * @param string  $target
     *
     * @return array
     */
    public static function getMetaTags(Request $request, string $target = 'blog'): array
    {
        $response = [];
        $data = $request->toArray();

        if ($target == 'filter') {
            if (array_key_exists('start', $data) || array_key_exists('end', $data) || array_key_exists('autor', $data) || array_key_exists('nakladnik', $data)) {
                array_push($response, Metatags::noFollow());
            }
        }

        if ($target == 'ap_filter') {
            if (array_key_exists('letter', $data)) {
                array_push($response, Metatags::noFollow());
            }
        }

        return $response;
    }


}
