<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all .env variables
        $envVariables = $_ENV;

        // Group the variables
        $groupedVariables = $this->groupEnvVariables($envVariables);

        // Define tab titles based on the groups
        $tabTitles = collect(array_keys($groupedVariables));
        foreach ($tabTitles as $title) {
            $tabContents[] = View('admin.Setting.systemsetting', ['groupName' => $title,'variables' => $groupedVariables[$title]
            ])->render();
        }

        // Pass the variables to the view
        return View('admin.CRUD.show', compact('tabTitles', 'tabContents'));
      //  return view('admin.Setting.systemsetting', compact('envVariables'));
    }

    private function groupEnvVariables($envVariables)
    {
        $groups = [
            'Database' => ['DB_', 'DATABASE_'],
            'Application' => ['APP_'],
            'Mail' => ['MAIL_'],
            'AWS' => ['AWS_'],
            'Pusher' => ['PUSHER_','MIX_PUSHER'],
            'MPESA' => ['MPESA_','SAFARICOM_'],
            // Add more groups as needed
        ];

        $groupedVariables = [];

        foreach ($envVariables as $key => $value) {
            $grouped = false;
            foreach ($groups as $groupName => $prefixes) {
                foreach ($prefixes as $prefix) {
                    if (strpos($key, $prefix) === 0) {
                        $groupedVariables[$groupName][$key] = $value;
                        $grouped = true;
                        break 2;
                    }
                }
            }
            if (!$grouped) {
                $groupedVariables['Other'][$key] = $value;
            }
        }

        return $groupedVariables;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function update(Request $request)
    {
        
        $envFile = app()->environmentFilePath();
        $envContents = file_get_contents($envFile);

        foreach ($request->except('_token') as $key => $value) {
            // Replace existing value
            $envContents = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=" . (strpos($value, ' ') !== false ? '"'.$value.'"' : $value),
                $envContents
            );

            // If the key doesn't exist, add it
            if (strpos($envContents, "{$key}=") === false) {
                $envContents .= "\n{$key}=" . (strpos($value, ' ') !== false ? '"'.$value.'"' : $value);
            }
        }

        file_put_contents($envFile, $envContents);

        // Clear config cache
        Artisan::call('config:clear');

    return redirect()->back()->with('success', 'System settings updated successfully.');
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
