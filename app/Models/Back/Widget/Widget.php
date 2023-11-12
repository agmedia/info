<?php

namespace App\Models\Back\Widget;

use App\Helpers\Helper;
use App\Models\Back\Catalog\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Widget extends Model
{

    /**
     * @var string
     */
    protected $table = 'widgets';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string[]
     */
    private $targets = [
        'blog' => 'Novosti',
        'info' => 'Info Stranice',
        'gallery' => 'Galerije'
    ];


    /**
     * @return string[]
     */
    public function getTargetResources(): array
    {
        return $this->targets;
    }


    /**
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $this->setRequest($request);

        return $this;
    }


    /**
     * @return mixed
     */
    public function store()
    {
        $id = $this->insertGetId($this->getModelArray());

        return $this->find($id);
    }


    /**
     * @return false
     */
    public function edit()
    {
        $updated = $this->update($this->getModelArray(false));

        if ($updated) {
            return $this;
        }

        return false;
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @param bool $insert
     *
     * @return array
     */
    private function getModelArray(bool $insert = true): array
    {
        $resource_data = $this->setResourceData();

        $response = [
            'resource'      => $this->request->group,
            'resource_data' => $resource_data,
            'title'         => $this->request->title,
            'subtitle'      => '',
            'slug'          => Str::slug($this->request->title),
            'status'        => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'    => Carbon::now()
        ];

        if ($this->request->data) {
            $filepath = Helper::resolveViewFilepath(Str::slug($this->request->title), 'widgets');

            Storage::disk('view')->put($filepath, $this->request->data);

            $response['data'] = $this->request->data;
        }

        if ($insert) {
            $response['created_at'] = Carbon::now();
        }

        return $response;
    }


    /**
     * Set Product Model request variable.
     *
     * @param $request
     */
    private function setRequest($request)
    {
        $this->request = $request;
    }


    /**
     * @return false|string
     */
    private function setResourceData()
    {
        $query = '';

        if ($this->request->query_string) {
            $query = str_replace('"', '', $this->request->query_string);
        }

        $data = [
            'new'        => (isset($this->request->new) and $this->request->new == 'on') ? 1 : 0,
            'popular'    => (isset($this->request->popular) and $this->request->popular == 'on') ? 1 : 0,
            'query'      => $query,
            'query_data' => (isset($this->request->query_data) and $this->request->query_data) ? $this->request->query_data : '',
            'items_list' => $this->request->has('action_list') ? $this->request->input('action_list') : []
        ];

        return json_encode($data);
    }
}