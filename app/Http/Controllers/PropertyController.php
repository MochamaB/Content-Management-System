<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FormDataTrait;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\UnitController;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;

    public function __construct()
    {
        $this->model = Property::class;

        $this->controller = collect([
            '0' => 'property', // Use a string for the controller name
            '1' => 'Property',
        ]);
    }



    public function index()
    {
        $tablevalues = Property::all();
     //   $tablevalues = Property::withUserUnits()->get();

        $mainfilter =  $this->model::pluck('property_name')->toArray();
        $viewData = $this->formData($this->model);
        $controller = $this->controller;
        /// TABLE DATA ///////////////////////////
        $tableData = [
            'headers' => ['PROPERTY', 'LOCATION', 'MANAGER', 'TYPE', 'ACTIONS'],
            'rows' => [],
        ];

        foreach ($tablevalues as $item) {
            $tableData['rows'][] = [
                'id' => $item->id,
                $item->property_name . ' - ' . $item->property_streetname,
                $item->property_location,
                $item->property_manager,
                $item->property_type,
            ];
        }

        return View(
            'admin.CRUD.form',
            compact('mainfilter', 'tableData', 'controller'),
            $viewData,
            ['controller' => $this->controller]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function create()
    {
        $viewData = $this->formData($this->model);

        return View('admin.CRUD.form', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Data Entry validation/////////////
        if (Property::where('property_name', $request->property_name)
            ->exists()
        ) {
            return redirect('admin.property.properties_index')->with('statuserror', 'Property is already in the system');
        } else {
            $validationRules = Property::$validation;
            $validatedData = $request->validate($validationRules);
            $property = new Property;
            $property->fill($validatedData);
            $property->save();

            return redirect('property')->with('status', 'Property Added Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        ////VARIABLES FOR CRUD TEMPLATES

        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);
        $tabTitles = collect([
            'Summary',
            'Units',
            'Utilities',
            //    'Maintenance',
            //    'Financials',
            //    'Users',
            //    'Invoices',
            //    'Payments'
            // Add more tab titles as needed
        ]);
        $amenities = $property->amenities;
        $allamenities = Amenity::all();

        $result = app('App\Http\Controllers\UnitController')->index($property);
        // Access the individual variables from the $result array
        $unitviewData = $result->getData();
        $resultUtilites = app('App\Http\Controllers\UtilitiesController')->index($property);
        // Access the individual variables from the $result array
        $utilitiesviewData = $resultUtilites->getData();

        // Access the variables directly from the array
        $tableData = $unitviewData['tableData'];
        $mainfilter = $unitviewData['mainfilter'];
        //   $viewData = $unitviewData['viewData'];






        $viewData = $this->formData($this->model, $property);
        //  $unitviewData = $result['unitviewData'];
        // Render the Blade views for each tab content
        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Summary') {
                $tabContents[] = View('admin.property.show_' . $title, $viewData, compact('amenities', 'allamenities'))->render();
            } elseif ($title === 'Units') {
                $tabContents[] = View(
                    'admin.CRUD.index',
                    $unitviewData,
                    compact('amenities', 'allamenities')
                )->render();
            } elseif ($title === 'Utilities') {
                $tabContents[] = View(
                    'admin.CRUD.index',
                    $utilitiesviewData,
                    compact('amenities', 'allamenities')
                )->render();
            }
        }

        return View('admin.CRUD.form', compact('pageheadings', 'tabTitles', 'tabContents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        $viewData = $this->formData($this->model, $property);
        $pageheadings = collect([
            '0' => $property->property_name,
            '1' => $property->property_streetname,
            '2' => $property->property_location,
        ]);

        return View('admin.CRUD.form', compact('pageheadings'), $viewData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Property $property, Request $request)
    {
        $validationRules = Property::$validation;
        $validatedData = $request->validate($validationRules);
        $property->fill($validatedData);
        $property->update();

        return redirect($this->controller['0'])->with('status', $this->controller['1'] . ' Edited Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function updateAmenities(Request $request, $id)
    {
        $property = Property::find($id);
        $property->amenities()->sync($request->amenities);

        return redirect()->back()->with('success', 'Amenities synced successfully!');
    }
}
