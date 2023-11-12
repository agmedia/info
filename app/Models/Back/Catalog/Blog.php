<?php

namespace App\Models\Back\Catalog;

use App\Models\Back\Settings\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Blog extends Model
{

    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Request
     */
    protected $request;


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'page_id')->select('id', 'title');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLast(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeGetSelectList(Builder $query, $count = 50): Builder
    {
        return $query->select(['id', 'title'])->last()->take($count);
    }


    /**
     * @return string
     */
    public function getTags(): string
    {
        $string = '';

        foreach ($this->tags as $tag) {
            $string .= $tag->title . ',';
        }

        return substr($string, 0, -1);
    }


    /**
     * Validate new category Request.
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

        $this->request = $request;

        return $this;
    }


    /**
     * Store new category.
     *
     * @return false
     */
    public function create()
    {
        $id = $this->insertGetId($this->createModelArray());

        if ($id) {
            $this->saveTags();

            return $this->find($id);
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return false
     */
    public function edit()
    {
        $id = $this->update($this->createModelArray('update'));

        if ($id) {
            $this->saveTags();

            return $this->find($this->id);
        }

        return false;
    }


    /**
     * @param string $method
     *
     * @return array
     */
    private function createModelArray(string $method = 'insert'): array
    {
        $response = [
            'category_id'       => null,
            'group'             => 'blog',
            'title'             => $this->request->title,
            'short_description' => $this->request->short_description,
            'description'       => $this->request->description,
            'meta_title'        => $this->request->meta_title,
            'meta_description'  => $this->request->meta_description,
            'slug'              => isset($this->request->slug) ? Str::slug($this->request->slug) : Str::slug($this->request->title),
            'keywords'          => null,
            'publish_date'      => $this->request->publish_date ? Carbon::make($this->request->publish_date) : null,
            'keywords'          => false,
            'status'            => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'        => Carbon::now()
        ];

        if ($method == 'insert') {
            $response['created_at'] = Carbon::now();
        }

        return $response;
    }


    /**
     * @return mixed
     */
    private function saveTags()
    {
        Tag::query()->where('page_id', $this->id)->delete();

        return Tag::insert($this->resolveTags());
    }


    /**
     * @return array
     */
    public function resolveTags(): array
    {
        $response = [];

        if ( ! empty($this->request->tags)) {
            $tags = explode(',', $this->request->tags);

            foreach ($tags as $tag) {
                $response[] = [
                    'page_id' => $this->id,
                    'title' => $tag,
                    'slug' => Str::slug($tag),
                    'related' => null,
                ];
            }
        }

        return $response;
    }


    /**
     * @param Category $category
     *
     * @return bool
     */
    public function resolveImage(Blog $blog)
    {
        if ($this->request->hasFile('image')) {
            $img = Image::make($this->request->image);
            $str = $blog->id . '/' . Str::slug($blog->title) . '-' . time() . '.';

            $path = $str . 'jpg';
            Storage::disk('blog')->put($path, $img->encode('jpg'));

            $path_webp = $str . 'webp';
            Storage::disk('blog')->put($path_webp, $img->encode('webp'));

            return $blog->update([
                'image' => config('filesystems.disks.blog.url') . $path
            ]);
        }

        return false;
    }
}
