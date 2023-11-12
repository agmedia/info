<?php

namespace App\Models\Back\Catalog\Gallery;

use Carbon\Carbon;
use App\Helpers\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{

    /**
     * @var string
     */
    protected $table = 'gallery_images';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Model
     */
    protected $resource;


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeBasic(Builder $query): Builder
    {
        return $query->select(['id', 'image', 'title', 'alt', 'sort_order']);
    }


    /**
     * @param $resource
     * @param $request
     *
     * @return mixed
     */
    public function store($resource, $request)
    {
        $this->resource = $resource;
        $existing       = isset($request['slim']) ? $request['slim'] : null;
        $new            = isset($request['files']) ? $request['files'] : null;

        // Ako ima novih slika
        if ($new) {
            foreach ($new as $new_image) {
                if (isset($new_image['image']) && $new_image['image']) {
                    $this->saveNew($new_image);
                }
            }
        }

        // Ako ima postoječih slika
        if ($existing) {
            foreach ($existing as $key => $image) {
                // Ako slika nije editirana
                $this->updateImageData($key, $image);

                if ($image['image']) {
                    $this->replace($key, $image);
                }
            }
        }

        return true;
    }


    /**
     * @param $id
     * @param $new
     *
     * @return mixed
     */
    public function replace(int $id, array $image)
    {
        // Nađi staru sliku, izdvoji naziv
        $old  = $this->where('id', $id)->first();
        $path = Image::cleanPath('gallery', $this->resource->id, $old->image);

        // Obriši staru i snimi novu fotku
        Image::delete('gallery', $this->resource->id, $path);
        $new_path = Image::save('gallery', $image, $this->resource);

        $updated = $this->where('image', $old->image)->update([
            'image' => config('filesystems.disks.gallery.url') . $new_path
        ]);

        if ( ! $updated) {
            return false;
        }

        return true;
    }


    /**
     * @param array $new_image
     *
     * @return bool
     */
    public function saveNew(array $new_image): bool
    {
        $path = Image::save('gallery', $new_image, $this->resource);

        $id = $this->insertGetId([
            'gallery_id' => $this->resource->id,
            'image'      => config('filesystems.disks.gallery.url') . $path,
            'title'      => $this->resource->title,
            'alt'        => $this->resource->alt,
            'published'  => 1,
            'sort_order' => (isset($new_image['sort_order']) && $new_image['sort_order']) ? $new_image['sort_order'] : 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        if ( ! $id) {
            return false;
        }

        return true;
    }


    /**
     * @param int   $id
     * @param array $image_data
     *
     * @return bool
     */
    public function updateImageData(int $id, array $image_data): bool
    {
        $original = $this->find($id);

        $id = $original->update([
            'title'      => $image_data['title'],
            'alt'        => $image_data['alt'],
            'default'    => (isset($image_data['default']) && $image_data['default']) ? 1 : 0,
            'published'  => (isset($image_data['published']) and $image_data['published'] == 'on') ? 1 : 0,
            'sort_order' => (isset($image_data['sort_order']) && $image_data['sort_order']) ? $image_data['sort_order'] : 0,
            'updated_at' => Carbon::now()
        ]);

        if ( ! $id) {
            return false;
        }

        return true;
    }

}
