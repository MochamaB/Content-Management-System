<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MyMedia;
use App\Models\Unit;
use Illuminate\Support\Facades\Session;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Model;
use App\Services\TableViewDataService;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $controller;
    protected $model;
    private $tableViewDataService;


    public function __construct(TableViewDataService $tableViewDataService)
    {
    
        $this->model = Media::class;

        $this->controller = collect([
            '0' => 'media', // Use a string for the controller name
            '1' => ' Media',
        ]);

        $this->tableViewDataService = $tableViewDataService;
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
        $mediadata = $this->model::all();
        $mainfilter =  $this->model::distinct()->pluck('collection_name')->toArray();
     //   $viewData = $this->formData($this->model);
     //   $cardData = $this->cardData($this->model,$invoicedata);
       // dd($cardData);
        $controller = $this->controller;
        $tableData = $this->tableViewDataService->getMediaData($mediadata,false);
        
        return View('admin.CRUD.form', compact('mainfilter', 'tableData', 'controller'),
      //  $viewData,
        [
         //   'cardData' => $cardData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Model $model = null)
    {
       
        if ($model) {
            // Retrieve the full model instance based on the ID
            $ModelInstance = Model::find($model->id);
        }
        dd($ModelInstance);
        
    
        Session::flash('previousUrl', request()->server('HTTP_REFERER'));
        return View('admin.media.create_media', compact('model'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $className = 'App\\Models\\' . $request->model; // replace with your models namespace
        $id = $request->id;
       // dd($className);
       if (class_exists($className)) {
            $model = $className::find($id);
                    if ($request->file('media')) {
                        $fieldName = 'media';
                        $mediaCollection = $request->collection_name; // Get the $request->media_type'
                //     dd($mediaCollection);
                    
                        $unit_id = $request->unit_id; // Get unit_id from request
                        $property_id = $request->property_id; // Get property_id from request
                    
                        $model->addMedia($request->file($fieldName))
                            ->withProperties(['unit_id' => $unit_id, 'property_id' => $property_id])
                            ->toMediaCollection($mediaCollection);
                    }
        }

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
