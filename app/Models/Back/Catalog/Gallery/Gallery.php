<?php

namespace App\Models\Back\Catalog\Gallery;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gallery extends Model
{

    /**
     * @var string
     */
    protected $table = 'galleries';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $appends = [];

    /**
     * @var Model
     */
    protected $request;


    /**
     * Gallery constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    /**
     * @param bool $all
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(bool $all = true)
    {
        if ($all) {
            return $this->hasMany(GalleryImage::class, 'gallery_id')->orderBy('sort_order');
        }

        return $this->hasMany(GalleryImage::class, 'gallery_id')->where('sort_order', '=', 0);
    }


    /**
     * Validate New Product Request.
     *
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
     * @return false
     */
    public function create()
    {
        //dd($this->request->target_id, $this->createModelArray());
        $id = $this->insertGetId($this->createModelArray());

        if ($id) {
            return $this->find($id);
        }

        return false;
    }


    /**
     * @return $this|false
     */
    public function edit()
    {
        $id = $this->update($this->createModelArray('update'));

        if ($id) {
            return $this;
        }

        return false;
    }


    /**
     * @param Gallery $gallery
     *
     * @return mixed
     */
    public function storeImages(Gallery $gallery)
    {
        return (new GalleryImage())->store($gallery, $this->request);
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param string $method
     *
     * @return array
     */
    private function createModelArray(string $method = 'insert'): array
    {
        //dd($this->request->toArray());
        $response = [
            'group'      => Str::slug($this->request->group) ?: '/',
            'title'      => $this->request->title,
            'target'     => Str::slug('Novosti'),
            'target_id'  => $this->request->target_id,
            'featured'   => (isset($this->request->featured) and $this->request->featured == 'on') ? 1 : 0,
            'status'     => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'sort_order' => 0,
            'updated_at' => Carbon::now()
        ];

        if ($method == 'insert') {
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


    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    public static function adminSelectList()
    {
        $response = [];
        $items    = static::all();

        foreach ($items as $item) {
            $response[] = [
                'id'    => $item->id,
                'title' => $item->title,
            ];
        }

        return collect($response);
    }
}
