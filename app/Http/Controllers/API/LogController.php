<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    /**
     * Restituisce la lista dei Log
     */
    public function getAdminList(Request $request)
    {
        if (!Gate::allows('viewAny', new Log())) {
            abort(403);
        }

        $data = Log::get();
        return DataTables::of($data)->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
}
