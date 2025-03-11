<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreHour;
use Inertia\Inertia;

class PageController extends Controller
{
    public function indexStoreHours()
    {
        $storeHours = StoreHour::all();

        return Inertia::render('admin/StoreHours', [
            'storeHours' => $storeHours,
        ]);
    }
}
