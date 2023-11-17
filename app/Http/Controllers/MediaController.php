<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Media;
use Illuminate\Support\Facades\Session;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $controller;
    protected $model;

    public function __construct()
    {
        $this->model = Media::class;

        $this->controller = collect([
            '0' => 'media', // Use a string for the controller name
            '1' => 'New Media',
        ]);
    }
    public function getMediaData($media)
    {
        $tableData = [
            'headers' => ['TITLE', 'CATEGORY', 'UPLOADED', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($media as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->name,
                $item->collection_name,
                $item->created_at,
            ];
        }

        return $tableData;
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.media.create_media');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);
        $model = Property::find(1);
        $model
            ->addMediaFromRequest('file')
            ->toMediaCollection('files');

            $previousUrl = Session::get('previousUrl');
            return redirect($previousUrl)->with('status', 'Media uploaded Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::find($id);
        $media->delete();

        return redirect()->back()->with('status', 'Media deleted successfully.');
    }
}
