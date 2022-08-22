<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{

    /**
     * Richiesta generale verifica dei permessi
     */
    public function check(Request $request, $permission, $model, $id = null)
    {
        $defModel = null;
        switch ($model) {
            case 'user':
                $defModel = new User();
                break;
            default:
                return $this->sendResponseAPI(false, 'Model not found');
        }

        if (is_null($id)) {
            $response = Gate::allows('viewAny', $defModel);
        } else if ($id == 'current-user') {
            $response = Gate::allows($permission, $request->user());
        } else {
            $elm = $defModel->where('id', $id)->first();
            if ($elm) {
                $response = Gate::allows($permission, $elm);
            } else {
                return $this->sendResponseAPI(false, 'Element not found');
            }
        }

        return $this->sendResponseAPI($response, (!$response) ? 'Not enough permissions' : '');
    }
}
