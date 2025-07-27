<?php

namespace App\Http\Controllers;

use App\Models\MenuGroup;
use App\Exports\MenuGroupExport;
use Maatwebsite\Excel\Facades\Excel;

class MenuGroupExcelController extends Controller
{
    public function export(MenuGroup $menuGroup)
    {
        return Excel::download(new MenuGroupExport($menuGroup), 'menu-group-' . $menuGroup->id . '.xlsx');
    }
}
