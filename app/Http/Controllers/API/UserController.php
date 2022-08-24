<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Register
     */
    public function register(Request $request)
    {

        $request->validate([
            'username' => 'required|unique:App\Models\User,username',
            'email' => 'required|email|unique:App\Models\User,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => 'required|date_format:Y-m-d'
        ]);

        try {
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->date_of_birth = $request->date_of_birth;
            $user->level = User::LEVEL_USER;
            $user->save();

            Auth::guard()->login($user);

            $user->sendEmailVerificationNotification();

            return $this->sendResponseAPI(true, 'A fresh verification link has been sent to your email address.');
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendResponseAPI(false, $ex->getMessage());
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->banned == 1) {
                return $this->sendResponseAPI(false, 'User is Banned');
            }

            $user->last_login = Carbon::now();
            $user->save();

            Log::write(Log::LEVEL_NOTICE, 'login', 'User', $user->id);

            return $this->sendResponseAPI(true, 'User login successfully');
        } else {
            return $this->sendResponseAPI(false, 'The provided credentials do not match our records.');
        }
    }

    /**
     * Recover Password
     */
    public function recover(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return $this->sendResponseAPI(true, $status);
        } else {
            return $this->sendResponseAPI(false, __($status));
        }
    }

    /**
     * Reimpostazione della password
     */
    public function recoverPost(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->sendResponseAPI(true, $status);
        } else {
            return $this->sendResponseAPI(false, __($status));
        }
    }

    /**
     * Verifica della mail di registrazione
     */
    public function emailVerify(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'hash' => 'required'
        ]);

        if (
            !hash_equals((string) $request->id, (string) $request->user()->getKey())
            || !hash_equals((string) $request->hash, sha1($request->user()->getEmailForVerification()))
        ) {
            return $this->sendResponseAPI(false, 'Error to validate email');

            \Session::flush();
        }

        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendResponseAPI(true, 'Email Verified');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        Log::write(Log::LEVEL_NOTICE, 'verifica email', 'User', $request->user()->id);

        return $this->sendResponseAPI(true, 'Email Verified');
    }

    /**
     * Reinvio mail
     */
    public function emailResend(Request $request)
    {
        if (!Auth::check()) {
            return $this->sendResponseAPI(false, 'User not logged');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->sendResponseAPI(true, 'A fresh verification link has been sent to your email address.');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            \Session::flush();
            return $this->sendResponseAPI(true, 'Successfully logged out');
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendResponseAPI(false, $ex->getMessage());
        }
    }

    /**
     * Recupero i dati dell'utente
     */
    public function getUserData(Request $request, $id = null)
    {
        if (!Auth::check()) {
            return $this->sendResponseAPI(false, 'User not logged');
        }

        if (is_null($id)) {
            $user = $request->user();
        } else {
            $user = User::where('id', $id)->first();
        }
        if (!Gate::allows('view', $user)) {
            abort(403);
        }

        return $this->sendResponseAPI(true, '', $user);
    }

    /**
     * Aggiornamento dati generali utente
     */
    public function updateUserDataGeneral(Request $request)
    {

        $value = $request->validate([
            'id' => ['required'],
            'username' => ['required', Rule::unique('users', 'username')->ignore($request->id, 'id'),],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($request->id, 'id'),],
            'date_of_birth' => 'required|date_format:Y-m-d',
            'motto' => ''
        ]);
        $user = User::where('id', $value['id'])->first();

        if (!Gate::allows('update', $user)) {
            abort(403);
        }
        $user->username = $value['username'];
        $user->email = $value['email'];
        $user->date_of_birth = $value['date_of_birth'];
        $user->motto = $value['motto'];

        $user->save();

        return $this->sendResponseAPI(true, 'Update successfully');
    }

    /**
     * aggiornamento della password
     */
    public function updateUserPassword(Request $request)
    {

        if (!Gate::allows('update', $request->user())) {
            abort(403);
        }
        $value = $request->validate([
            'id' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);
        $user = User::where('id', $value['id'])->first();

        if (!Gate::allows('update', $user)) {
            abort(403);
        }

        $user->password = Hash::make($value['password']);
        $user->save();

        return $this->sendResponseAPI(true, 'Update successfully');
    }

    /**
     * Restituisce la lista degli utenti
     */
    public function getAdminListUsers(Request $request)
    {
        if (!Gate::allows('viewAny', new User())) {
            abort(403);
        }

        $data = User::withTrashed()->get();
        return DataTables::of($data)->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Gestione ban utente
     */
    public function ban(Request $request)
    {
        $value = $request->validate([
            'id' => ['required'],
            'note' => []
        ]);
        $user = User::where('id', $value['id'])->first();

        if (!Gate::allows('ban', $user)) {
            abort(403);
        }
        if ($user->banned) {

            Log::write(Log::LEVEL_NOTICE, 'unban', 'User', $user->id, $value['note']);
            $user->banned = false;
            $user->banned_at = null;
            $user->save();

            return $this->sendResponseAPI(true, 'Update unbanned successfully');
        } else {
            Log::write(Log::LEVEL_NOTICE, 'ban', 'User', $user->id, $value['note']);
            $user->banned = true;
            $user->banned_at = Carbon::now();
            $user->save();
            return $this->sendResponseAPI(true, 'Update banned successfully');
        }
    }
}
