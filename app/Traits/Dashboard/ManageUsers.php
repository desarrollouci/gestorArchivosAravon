<?php 

namespace App\Traits\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Menu;
use App\Models\Measure;
use App\Models\Picture;
use App\Models\Weight;
use App\Models\Clinic;
use App\Models\Data;
use App\Models\PersonalForm;
use App\Http\Requests\UserRequest;
use App\Helpers\Uploader;
use App\Http\Resources\User as UserResource;
use Hash;
use DB;
use Str;
use Carbon;

trait ManageUsers {

	public function users(){

        try {
            $this->authorize('haveaccess','user.index');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }

        /* if(!request('page')){
             $this->resetSessions();
        }; */

        if (request('page')) {
            session()->put('page[users]', request('page'));
            session()->save();
        }

        $view = 'dashboard.users.index';
       
        if (request('search')) {
            session()->put('search[users]', request('search'));
            session()->save();
        }

        if (request('type')) {
            session()->put('type[users]', request('type'));
            session()->save();
        }
        
        if (request('filter')) {
            session()->put('filter[users]', request('filter'));
            session()->save();
        }
        
        session()->put('view[usersEdit]', 'menus');
        session()->save();

        $users = User::Filtered();
        
        if(request()->ajax()){
           $view = 'dashboard.users.table'; 
        }

        return view($view, compact('users'));
    }

    public function showUser(User $user) {

        try {
            $this->authorize('haveaccess','user.show');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        
        session()->put('wiew', 'widgets');
        session()->save();

        $title = __('<i class="fas fa-id-card"></i> Usuario :user', ["user" => $user->name]);
        $view = 'show';
        $textButton = '';
        $options = [];
        return view('dashboard.users.show', compact('title', 'user','view', 'options','textButton'));

    }

    public function createUser() {

        try {
            $this->authorize('haveaccess','user.create');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        
        $user = new User;
        //$user->role = User::CUSTOMER;
        //$user->locked = "0";
        
        $title = __('<i class="fas fa-user-plus"></i> Crear usuario');
        $textButton = __("Guardar");
        $options = ['route' => ['admin.dashboard.users.store'], 'files' => true];
        $required = false;
        $view = 'post';
        return view('dashboard.users.create', compact('title','view', 'user', 'options', 'textButton', 'required'));

    }

    public function storeUser(UserRequest $request) {
      //dd(request()->all());
        try {
            $this->authorize('haveaccess','user.create');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para realizar esta acción')]);
            return back();
        }

        try {
            DB::beginTransaction();
            
            $file = null;
            if ($request->hasFile('avatar')) {
                $file = Uploader::uploadFile('avatar', 'users');
            }
            
            $user = User::create($this->userInput($file, true));

            $role_id = auth()->user()->role == User::ADMIN ? request("role_id") : User::CUSTOMER_ID;
            $user->roles()->sync($role_id);
            $user->data_user()->create($this->dataUserInput());

            DB::commit();

            session()->flash("message", ["success", __("Usuario creado satisfactoriamente")]);
            return redirect(route('admin.dashboard.users.edit', ['user' => $user]));
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }
    }

    public function editUser(User $user) {
        
        try {
            $this->authorize('haveaccess','user.edit');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        
        if(!request('page')){
            $this->resetSessions();
        };
        
        if (request('search')) {
            session()->put('search[usersMenu]', request('search'));
            session()->save();
        }

        if (request('type')) {
            session()->put('type[usersMenu]', request('type'));
            session()->save();
        }
        
        if (request('filter')) {
            session()->put('filter[usersMenu]', request('filter'));
            session()->save();
        }

        $page = 'dashboard.users.edit';

        if(request()->ajax()){
           $page  = 'dashboard.users.menus.table';
        }

        $menus = Menu::forCustomer($user);
        $title = __('<i class="fas fa-user-edit"></i> Editar :user', ["user" => $user->name]);
        $textButton = __("Actualizar");
        $options = ['route' => ['admin.dashboard.users.update', ["user" => $user]], 'files' => true];

        /* measures */
        $user->with('data_user')->with('dossiers');
        $optionsMeasures = ['route' => ['admin.dashboard.measures.add_measures_user', ["user" => $user]]];
        $optionsBody = ['route' => ['admin.dashboard.measures.add_weight_user', ["user" => $user]]];
        $optionsUploadImage = ['route' => ['admin.dashboard.measures.upload_image', ["user" => $user]], 'files' => true, 'id' => 'form-image'];
        $measures = Measure::whereUserId($user->id)->latest()->first();
        $weights = Weight::whereUserId($user->id)->latest()->first();
        $images = Picture::Filtered($user->id);    
        $first_image = Picture::whereUserId($user->id)->first();
        /*end measures */

        /* Clinic*/
        $analytic = Clinic::whereUserId($user->id)->latest()->first();
        $optionsUploadClinic = ['route' => ['admin.dashboard.measures.upload_clinic', ["user" => $user]], 'files' => true];
        /*end clinic*/

        /*form register*/
        $form = PersonalForm::whereUserId($user->id)->latest()->first();
        if(is_null($form)){
            $form = $user->personal_form()->create($this->NewFormInput());
            $form = PersonalForm::whereUserId($user->id)->latest()->first();
           
        }
        
        /* end form */
        $view = 'put';
        $required = false;
        $update = true;
        return view($page, compact('title', 'user','form', 'menus', 'images','first_image', 'measures', 'weights', 'analytic', 'options', 'optionsMeasures', 'optionsBody','optionsUploadImage','optionsUploadClinic', 'textButton', 'view', 'required', 'update'));
    }

    public function updateUser(UserRequest $request, User $user) {

        try {
            $this->authorize('haveaccess','user.edit');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para para realizar esta acción')]);
            return back();
        }
            
       
        try {
            DB::beginTransaction();
            $file = $user->avatar;
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Uploader::removeFile("user", $user->avatar);
                }
                $file = Uploader::uploadFile('avatar', 'users');
            }

               /*  session()->put('view[usersEdit]', 'menus');
                session()->save(); */

                $user->fill($this->userInput())->save();

                $role_id = auth()->user()->role == User::ADMIN ? request("role_id") : User::CUSTOMER_ID;
                $user->roles()->sync($role_id);
                $user->data_user()->update($this->dataUserInput());
            DB::commit();

            session()->flash("message", ["success", __("Usuario actualizado satisfactoriamente")]);
            return back();
        } catch (\Throwable $exception) {
            DB::rollBack();
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }
    }

    public function uploadPicture(User $user){
        try {
            DB::beginTransaction();
             
        
            $file = null;
            if (request()->hasFile("picture")) {
                $file = Uploader::uploadFile('picture', 'users/' . $user->id, $user->id);
            }
            
            $user->pictures()->create([
                'picture' => $file
            ]); 
                
            session()->put('view[usersEdit]', 'evolution');
            session()->save();

            DB::commit();
            
            
        } catch (\Throwable $exception) {
            if (request()->ajax()) {
                return response()->json(['type' => "error", 'message' => $exception]);
            } else {
                session()->flash("message", ["danger", $exception->getMessage()]);

                return back()->withInput();
            }
        }

        if (request()->ajax()) {

            $user->with('data_user');
            $images = Picture::Filtered($user->id);
            $first_image = Picture::whereUserId($user->id)->first();
            
            $page = 'partials.customer.images.carousel';
            return view($page, compact('user', 'images', 'first_image'));
        } else {
            session()->flash("message", ["success", __("Imagen subida satisfactoriamente")]);
            return back();
        }

        

    }

    public function uploadClinic(User $user){
        try {
            DB::beginTransaction();
            
            $file = null;
            if (request()->hasFile("clinic")) {
                $file = Uploader::uploadFile('clinic', 'clinics/' . $user->id, $user->id);
            }
            
            $user->clinics()->create([
                'clinic' => $file
            ]); 
                
            session()->put('view[usersEdit]', 'clinic');
            session()->save();

            DB::commit();
            session()->flash("message", ["success", __("Analítica subida satisfactoriamente")]);
            return back();
            
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }    

    }

    public function updatePersonalForm(PersonalForm $form){
               
        try {
            DB::beginTransaction();

            session()->put('view[usersEdit]', 'register_form');
            session()->save();
           
            //$p_form = PersonalForm::create($this->formInput());
            $form->form_data = json_encode(request()->except('_token', 'clinic', '_method'));
            $form->save();
            DB::commit();

            session()->flash("message", ["success", __("Formulario actualizado satisfactoriamente")]);
            return back()->withInput();
            
        } catch (\Throwable $exception) {
            DB::rollBack();
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }        
    }

    public function destroyUser(User $user) {
       
        try {
            $this->authorize('haveaccess','user.destroy');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para para realizar esta acción')]);
            return back();
        }

        if (request()->ajax()) {
            try {
           
                DB::beginTransaction();
                $user->locked = $user->locked == 0 ? 1 : 0;
                
                $user->save();
                
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                session()->flash("message", ["danger", $exception->getMessage()]);
                return back();
            }
        } else {
            abort(401);
        }
        

        session()->flash("message", [
            "success",
            __("El usuario :user ha sido :locked correctamente", [
                "user" => $user->name,
                'locked' => $user->locked == 0 ? 'desbloquedo' : 'bloqueado'
            ])
        ]);
    }

    protected function userInput(String $file = null, bool $create = false): array {

        $data = [
            "role" => auth()->user()->role == User::ADMIN ? request("role") : User::CUSTOMER,
            "name" => e(request("name")),
            "email" => e(request("email")),
            "gender" => request("gender"),
            "complete_form" => (int)request()->has("complete_form"),
            "tracing" => (int)request()->has("tracing"),
           
        ];

        if(!is_null(request('password'))){
           
            $data["password"] = Hash::make(request('password'));
            $data["password_code"] =  e(request("password"));
        }

        if($create){
            $data["email_verified_at"] = now();
            $data["verification_code"] = sha1( time() );
        }

        return $data;
    }

    protected function newFormInput(): array {

        return [
            'form_data' => '{"q1":"1","q2":"1","q3":"1","q4":"1","q5":"1","q6":"1","q7":"1","q8":"1","q9":"1","q10":{"other":{"lunch":null,"dinner":null}},"q11":{"fruits":"1","vegetables":"1","meat":"1","fish":"1","shellfish":"1","dairy":"1","rice":"1","legumes":"1","sweet_foods":"1","eggs":"1","nuts":"1","alcoholic":"1","packaged":"1"},"q12":{"fruits":{"like":["null","null","null"],"not_like":["null","null","null"]},"vegetables":{"like":["null","null","null"],"not_like":["null","null","null"]},"meats":{"like":["null","null","null"],"not_like":["null","null","null"]},"seafoods":{"like":["null","null","null"],"not_like":["null","null","null"]},"dairy":{"like":["null","null","null"],"not_like":["null","null","null"]},"legumbes":{"like":["null","null","null"],"not_like":["null","null","null"]},"nuts":{"like":["null","null","null"],"not_like":["null","null","null"]}},"q13":{"break":{"freq":{"yes":[null,null,null],"no":[null,null]}},"brunch":{"freq":{"yes":[null,null,null],"no":[null,null]}},"lunch":{"freq":{"yes":[null,null,null],"no":[null,null]}},"snack":{"freq":{"yes":[null,null,null],"no":[null,null]}},"dinner":{"freq":{"yes":[null,null,null],"no":[null,null]}}},"q14":{"week":{"break":{"where":"1","when":null},"brunch":{"where":"1","when":null},"lunch":{"where":"1","when":null},"snack":{"where":"1","when":null},"dinner":{"where":"1","when":null}},"weekend":{"break":{"where":"1","when":null},"brunch":{"where":"1","when":null},"lunch":{"where":"1","when":null},"snack":{"where":"1","when":null},"dinner":{"where":"1","when":null}}},"q15":"1","q16":"1","q17":"1","q18":"1","q19":["1",null],"q20":"1","q21":"1","q22":"1","q23":[null,null,null],"q24":null,"q25":["1",null],"q26":["1",null],"q27":"1","q28":{"lunch":[{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null},{"time":null,"who":null,"food":null,"qty":null}]}}',
        ];
    }

    protected function dataUserInput(): array {

        $data = [
            "phone" => request("phone"),
            "loss_fat" => request("loss_fat"),
            "body_type" => request("body_type"),
            "height" => request("height"),
            "initial_weight" => request("initial_weight"),
            "objective" => request("objective"),
            "birth" => Carbon\Carbon::parse(request("birth"))->format('Y-m-d'),
        ];

        return $data;
    }

    protected function formInput(): array {

        return [
            'form_data' => json_encode(request()->except('_token', 'clinic', 'method')),
        ];
    }
    
    /* JSON */
    public function jsonUser(User $user){
    
        $u = User::whereId($user->id)->get();
        if (request()->ajax()) {
           return UserResource::collection($u);
        }
    }

    public function saveData(){
        if (request()->ajax()) {
            $data = Data::whereUserId(request('user_id'))->first();
            $data->energy_expenditure = request('energy_expenditure');
            $data->diets_numbers = request('diets_numbers');
            $data->coe_pro = request("coe_pro");
            $data->coe_fat = request("coe_fat");
            $data->breakfast = request("breakfast") * 100;
            $data->brunch = request("brunch") * 100;
            $data->lunch = request("lunch") * 100;
            $data->snack = request("snack") * 100;
            $data->dinner = request("dinner") * 100;
            $data->save();
            
            return response()->json([
                    'status'=>'success',
                    'msg'=> __('Datos de calculo fijados'),
            ]);
            
        }
    }

}