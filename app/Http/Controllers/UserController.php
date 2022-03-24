<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(User $user){
        $user->with('files');
        return view('users.view', compact('user'));
    }

    public function update(User $user){

        try {
            DB::beginTransaction();
                $user->fill(request()->all())->save();
            DB::commit();
                session()->flash("message", ["success", __("Usuario actualizado satisfactoriamente")]);
        } catch (\Throwable $exception) {
            DB::rollBack();
                session()->flash("message", ["danger", $exception->getMessage()]);
        }

        
        return back();
    }

    protected function create()
    {   

        $validator = Validator::make(Request()->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required','min:8'],
            'password_confirmation' => 'required|min:8|same:password',
        ],
        [
            'name.required' => 'Su nombre es obligatorio.',
            'email.required' => 'Su correo electrónico es obligatorio.',
            'email.email' => 'El formato de su correo electrónico no es correcto.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'password.required' => 'La contraseña en obligatoria',
            'password.same' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña de demasiado corta, minímo 8 caracteres',
            'password_confirmation.required' => 'La confirmación de la contraseña en obligatoria',
            'password_confirmation.same' => 'Las contraseñas no coinciden',
            'password_confirmation.min' => 'La contraseña de demasiado corta, minímo 8 caracteres',
            
        ]);

        
        if ($validator->fails()) {
           
            session()->flash('error-register');
           
            return back()->withErrors($validator)->withInput();
        }
        try {
            DB::beginTransaction();
                User::create([
                    'name' => request('name'),
                    'email' => request('email'),
                    'password' => Hash::make(request('password')),
                ]);
            DB::commit();
                session()->flash("message", ["success", __("Usuario creado satisfactoriamente")]);
        } catch (\Throwable $exception) {
            DB::rollBack();
                session()->flash("message", ["danger", $exception->getMessage()]);
        }

        
        return back();
    }
    
}
