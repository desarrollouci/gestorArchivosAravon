<?php
namespace App\Traits\Customer;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use App\Models\Measure;
use App\Models\Picture;
use App\Models\Clinic;
use App\Models\PersonalForm;
use App\Helpers\Uploader;
use App\Models\Data;
use Carbon\Carbon;
use Auth, DB, Hash;

trait ManageUser {
    
    public function profile() {
        $user = User::whereId(auth()->id())->with('data_user')->first();
        $menus = Menu::whereUserId(auth()->id())->withCount('products')->where('locked', 0)->orderBy('created_at', 'DESC')->get();
        $form = PersonalForm::whereUserId($user->id)->latest()->first();
        $noSubmit = true;
        return view('web.customer.profile', compact('user', 'menus','form', 'noSubmit'));
    }

	public function personalForm(User $user){
        
        //$user = Auth()->user();

        try {
            DB::beginTransaction();

            $file = null;
            if (request()->hasFile("clinic")) {
                $file = Uploader::uploadFile('clinic', 'clinics/' . $user->id, $user->id);

                $user->clinics()->create([
                    'clinic' => $file
                ]); 

            }
            //$p_form = PersonalForm::create($this->formInput());
            $user->personal_form()->create($this->formInput());
            $user->complete_form = true;
            $user->save();
           
            DB::commit();
            session()->flash("message", ["success", __("Formulario enviado satisfactoriamente")]);
            return back()->withInput();
            
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back()->withInput();
        }        
    }
    
    public function evolutionRating(){
        $data_user = Data::where('user_id', auth()->id())->first();

        $data_user->rating = (float)request('rating');
        $data_user->save();
        
    }

    protected function formInput(string $file = null): array {

        return [
            'form_data' => json_encode(request()->except('_token', 'clinic')),
        ];
    }
    

}
