<?php 

namespace App\Traits\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Role, App\Models\Permission;
use App\Http\Requests\RoleRequest;
use DB;

trait ManageRoles {

	public function roles(){

        try {
            $this->authorize('haveaccess','role.index');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        

        $roles = Role::filtered();
        return view('dashboard.roles.index', compact('roles'));
    }

    public function createRole() {

        try {
            $this->authorize('haveaccess','role.create');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        

        $role = new Role;
        $permissions = Permission::get();
        $title = __("Crear Role");
        $textButton = __("Guardar");
        $options = ['route' => ['admin.dashboard.roles.store'], 'files' => true];
        return view('dashboard.roles.create', compact('title','permissions', 'role', 'options', 'textButton'));

    }

    public function storeRole(RoleRequest $request) {
       
        try {
            $this->authorize('haveaccess','role.create');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para realizar esta acción')]);
            return back();
        }

        try {
            DB::beginTransaction();
            
            $role = Role::create($this->roleInput());
            $role->permissions()->sync(request("permission"));

            DB::commit();
            session()->flash("message", ["success", __("Role creado satisfactoriamente")]);
            return redirect(route('admin.dashboard.roles.edit', ['role' => $role]));
        } catch (\Throwable $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back();
        }
    }

    public function editRole(Role $role) {

        try {
            $this->authorize('haveaccess','role.edit');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para entrar en esta sección')]);
            return back();
        }
        

        $role->load("permissions");
        $permissions = Permission::get();
        $title = __("Editar el :role", ["role" => $role->name]);
        $textButton = __("Actualizar role");
        $options = ['route' => ['admin.dashboard.roles.update', ["role" => $role]], 'files' => true];
        $update = true;
        return view('dashboard.roles.edit', compact('title', 'role', 'permissions', 'options', 'textButton', 'update'));
    }

    public function updateRole(RoleRequest $request, Role $role) {

        try {
            $this->authorize('haveaccess','role.edit');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para realizar esta acción')]);
            return back();
        }
       
        
        try {
            DB::beginTransaction();
                $role->fill($this->roleInput())->save();
                $role->permissions()->sync(request("permission"));
            DB::commit();

            session()->flash("message", ["success", __("Role actualizado satisfactoriamente")]);
            return back();
        } catch (\Throwable $exception) {
            DB::rollBack();
            session()->flash("message", ["danger", $exception->getMessage()]);
            return back();
        }
    }

    public function destroyRole(Role $role){ 

        try {
            $this->authorize('haveaccess','role.destroy');
        } catch (\Exception $e){
            session()->flash("message", ["danger", __('No tienes permiso para realizar esta acción')]);
            return back();
        }
        
        
        try {
            if (request()->ajax()) {
               
                $role->delete();
                session()->flash("message", ["success", __("El role :role ha sido eliminada correctamente", ["role" => $role->name])]);
            } else {
                abort(401);
            }
        } catch (\Exception $exception) {
            session()->flash("message", ["danger", $exception->getMessage()]);
        }
    }

    protected function roleInput(): array {
        return [
            "name" => request("name"),
            "slug" => request("slug"),
            "description" => request("description"),
            "full-access" => request("full-access") == 'on' ? 'yes' : 'no',
        ];
    }
    
}