<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Lease;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $cardData = [];
      
        if ($user->hasRole('admin') || Gate::allows('view-all', $user)) 
        {
            $cardData = $this->getAdminCardData();
        }

        return View('admin.Report.dashboard',compact('cardData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    
     private function getAdminCardData()
     {
         $propertyCount = Property::count();
         $unitCount = Unit::count();
         $leaseCount = Lease::count();
         $percentage = ($unitCount > 0) ? round(($leaseCount / $unitCount) * 100) : 0;
 
         // Add other data retrieval logic for admin role.
 
         // Structure the data with card type information.
         $cards = [
             'All Properties' => 'information',
             'Units' => 'progress',
             // Add other card types for admin role.
         ];
 
         $data = [
             'All Properties' => $propertyCount,
             'Units' => [
                 'modelCount' => $unitCount,
                 'modeltwoCount' => $leaseCount,
                 'percentage' => $percentage,
                 // Add other data points related to maintenanceCount card.
             ],
             // Add other card data for admin role.
         ];
 
         return ['cards' => $cards, 'data' => $data];
     }


    public function create()
    {
        //
    }

    public function cards()
    {
        return View('admin.CRUD.cards_template');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }
}
