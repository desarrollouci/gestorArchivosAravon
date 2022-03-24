<!-- Modal -->
<div class="modal fade" id="register-modal" tabindex="-1" aria-labelledby="register-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="register-modalLabel">Añadir cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        
        @if (count($errors) > 0)
            <div class = "alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <x-form method="POST" :action="route('users.register')">
        @csrf
                <div class="row">
                    <div class="col-md-6">
                        <x-field id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" label="Nombre" value="{{ old('name') }}" required autocomplete="name" autofocus></x-field>
                    </div> 
                    <div class="col-md-6">
                        <x-field id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" label="Email" value="{{ old('email') }}" required autocomplete="email"></x-field>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-field name="password" type="text" label="Contraseña" id="password" required autocomplete="new-password"></x-field>
                    </div> 
                    <div class="col-md-6">
                        <x-field id="password-confirm" type="password" label="Confirmar contraseña" name="password_confirmation" required autocomplete="new-password"></x-field>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary float-end">{{ __('Guardar') }}</button>
    </x-form>
        </div>
      </div>
    </div>
  </div>