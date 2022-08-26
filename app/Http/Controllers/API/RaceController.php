<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class RaceController extends Controller
{
    /**
     * Restituisce la lista delle razze
     */
    public function getAdminList(Request $request)
    {
        if (!Gate::allows('viewAny', new Race())) {
            abort(403);
        }

        $data = Race::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Recupera le informazioni della razza
     */
    public function getRaceData(Request $request, $slug)
    {
        $race = Race::where('slug', $slug)->first();
        if ($race) {
            return $this->sendResponseAPI(true, '', $race);
        } else {
            return $this->sendResponseAPI(false, 'Element not found');
        }
    }

    /**
     * crea o aggiorna la razza
     */
    public function updateRace(Request $request, $slug = null)
    {
        if ($slug) {
            $race = Race::where('slug', $slug)->first();
            if (!Gate::allows('update', $race)) {
                abort(403);
            }
        } else {
            if (!Gate::allows('create', new Race())) {
                abort(403);
            }
            $race = new Race();
        }

        //todo: validazione e creazione
    }
}
