<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Helpers\Image;
use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Action\Action;
use App\Models\Back\Catalog\Blog;
use App\Models\Back\Catalog\Gallery\Gallery;
use App\Models\Back\Catalog\Gallery\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $galleries = Gallery::paginate(12);

        return view('back.catalog.gallery.index', compact('galleries'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Gallery::groupBy('group')->pluck('group');
        $targets = Blog::query()->getSelectList()->get();

        return view('back.catalog.gallery.edit', compact('groups', 'targets'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $gallery = new Gallery();

        $stored = $gallery->validateRequest($request)->create();

        if ($stored) {
            $gallery->storeImages($stored);

            return redirect()->route('gallery.edit', ['gallery' => $stored])->with(['success' => 'Gallery was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the gallery.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Author $author
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        $groups = Gallery::groupBy('group')->pluck('group');
        $targets = Blog::query()->getSelectList()->get();
        $existing = $gallery->images()->get()->toArray();

        //dd($existing);

        return view('back.catalog.gallery.edit', compact('gallery', 'groups', 'targets', 'existing'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Author                   $author
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        $updated = $gallery->validateRequest($request)->edit();

        if ($updated) {
            $gallery->storeImages($updated);

            return redirect()->route('gallery.edit', ['gallery' => $updated])->with(['success' => 'Gallery was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the gallery.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Action $action)
    {
        $destroyed = Action::destroy($action->id);

        if ($destroyed) {
            return redirect()->route('actions')->with(['success' => 'Akcija je uspjšeno izbrisana!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Greška prilikom brisanja.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyApi(Request $request)
    {
        if ($request->has('id')) {
            $gallery = Gallery::find($request->input('id'));
            $destroyed = $gallery->delete();

            if ($destroyed) {
                return response()->json(['success' => 200]);
            }
        }

        return response()->json(['error' => 300]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyImage(Request $request)
    {
        if ($request->has('data')) {
            $image = GalleryImage::find($request->input('data'));

            $deleted = GalleryImage::where('gallery_id', $image->gallery_id)->where('image', $image->image)->delete();

            if ($deleted) {
                $path = Image::cleanPath('gallery', $image->gallery_id, $image->image);
                Image::delete('gallery', $image->gallery_id, $path);

                return response()->json(['success' => 200]);
            }
        }

        return response()->json(['error' => 400]);
    }
}
