<?php

namespace App\Traits;

use App\Models\Setting;
use Carbon\Carbon;

trait SettingTrait
{
    public function showSettings($setting)
    {
        $tabTitles = collect([
            'Global Settings',
            'Overrides',
        ]);

        $controller = 'setting';

        $globalSettings = Setting::where('name', $setting->name)
            ->whereNull('model_id')
            ->get();
        $individualSetting = Setting::where('name', $setting->name)
            ->whereNotNull('model_id')
            ->get();

        $settingsTableData = $this->tableViewDataService->getSettingData($individualSetting);

        $tabContents = [];
        foreach ($tabTitles as $title) {
            if ($title === 'Global Settings') {
                $tabContents[] = view('admin.Setting.global_settings', compact('setting', 'globalSettings'))->render();
            } elseif ($title === 'Overrides') {
                $tabContents[] = view('admin.CRUD.index_show', ['tableData' => $settingsTableData, 'controller' => ['setting']])->render();
            }
        }

        return ['tabTitles' => $tabTitles, 'tabContents' => $tabContents, 'controller' => $controller];
    }
  
}