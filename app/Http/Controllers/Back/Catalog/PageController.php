<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Helpers\Helper;
use App\Http\Controllers\Back\Settings\Author;
use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Page;
use App\Models\Back\Widget\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = Page::query()->whereNotNull('subgroup')->distinct()->pluck('subgroup');
        $pages  = Page::adminSearch($request)->paginate(12);

        return view('back.catalog.pages.index', compact('pages', 'groups'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Page::subgroups(null, 'special')->pluck('subgroup');

        return view('back.catalog.pages.edit', compact('groups'));
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
        $page = new Page();

        $stored = $page->validateRequest($request)->create();

        if ($stored) {
            $page->resolveImage($stored);

            $this->flush($stored);

            return redirect()->route('pages.edit', ['page' => $stored])->with(['success' => 'Page was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the page.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Author $author
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        $groups = $page->subgroups(null, 'special')->pluck('subgroup');
        $resources     = (new Widget())->getTargetResources();
        $resource_data = json_decode($page->resource_data, true);

        $filepath = Helper::resolveViewFilepath($page->slug, 'pages');
        $storage  = Storage::disk('view');

        if ($storage->exists($filepath)) {
            $page->description = $storage->get($filepath);
        }

        return view('back.catalog.pages.edit', compact('page', 'groups', 'resources', 'resource_data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Author                   $author
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $updated = $page->validateRequest($request)->edit();

        if ($updated) {
            $page->resolveImage($updated);

            $this->flush($updated);

            return redirect()->route('pages.edit', ['page' => $updated])->with(['success' => 'Page was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the page.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Page $page)
    {
        $destroyed = Page::destroy($page->id);

        if ($destroyed) {
            return redirect()->route('pages')->with(['success' => 'Page was succesfully deleted!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error deleting the page.']);
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
            $destroyed = Page::destroy($request->input('id'));

            if ($destroyed) {
                return response()->json(['success' => 200]);
            }
        }

        return response()->json(['error' => 300]);
    }


    /**
     * @param Page $page
     */
    private function flush(Page $page): void
    {
        Cache::forget('page.' . $page->id);
        Cache::forget('page.' . $page->slug);
    }
}
