<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreHour;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $storeHours = StoreHour::all();

        return Inertia::render('Admin/StoreHours', [
            'storeHours' => $storeHours,
        ]);
    }
}
