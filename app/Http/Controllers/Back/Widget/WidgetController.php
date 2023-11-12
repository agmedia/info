<?php

namespace App\Http\Controllers\Back\Widget;

use App\Helpers\Helper;
use App\Models\Back\Catalog\Publisher;
use App\Models\Back\Settings\Settings;
use App\Models\Back\Widget\Widget;
use App\Models\Back\Widget\WidgetGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WidgetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Widget::query();

        if ($request->has('search')) {
            $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
        }

        $widgets = $query->orderByDesc('updated_at')
                         ->paginate(config('settings.pagination.items'));

        return view('back.widget.index', compact('widgets'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $resources = (new Widget())->getTargetResources();
        $resource_data = [];

        return view('back.widget.edit', compact('resources', 'resource_data'));
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
        $widget = new Widget();
        $stored = $widget->validateRequest($request)->store();

        if ($stored) {
            return redirect()->route('widgets')->with(['success' => 'Widget je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! Desila se greška sa snimanjem widgeta.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Widget $widget)
    {
        $resources     = $widget->getTargetResources();
        $resource_data = json_decode($widget->resource_data, true);

        $data     = $widget->data;
        $filepath = Helper::resolveViewFilepath($widget->slug, 'widgets');
        $storage  = Storage::disk('view');

        if ($storage->exists($filepath)) {
            $data = $storage->get($filepath);
        }

        return view('back.widget.edit', compact('widget', 'resources', 'resource_data', 'data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Widget $widget)
    {
        //dd($request->toArray());
        $updated = $widget->validateRequest($request)->edit();

        if ($updated) {
            $this->flush($widget);

            return redirect()->back()->with(['success' => 'Widget je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! Desila se greška sa snimanjem widgeta.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id') && $request->input('id')) {
            try {
                Widget::query()->where('id', $request->input('id'))->delete();
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }

            return response()->json(['success' => 200]);
        }

        return response()->json(['error' => 300]);
    }


    /**
     * @param Page $page
     */
    private function flush(Widget $widget): void
    {
        Cache::forget('wg.' . $widget->id);
    }


    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/
    // API ROUTES

    public function getLinks(Request $request)
    {
        if ($request->has('type')) {
            if ($request->input('type') == 'category') {
                return response()->json(Category::getList());
            }
            if ($request->input('type') == 'page') {
                return response()->json(Blog::published()->pluck('title', 'id'));
            }
            if ($request->input('type') == 'publisher') {
                return response()->json(Publisher::getList());
            }
        }

        return response()->json([
            'id' => 0,
            'text' => 'Molimo odaberite tip linka..'
        ]);
    }
}
