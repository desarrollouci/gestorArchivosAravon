@extends('layouts.app')
    
@push('css')
    <style>
        .card-body > .list-group{
            max-height: 300px;
        }
    </style>
@endpush

@section('content')
    <div class="container bg-transparent">
        <div class="card mb-3">
            <x-form method="put" :action="route('users.update', $user)">
                <div class="card-header bg-transparent">
                    {{ __("Ficha del cliente") }}
                    <a href="{{route('home')}}" class="text-decoration-none">
                        <div class="btn btn-dark float-end position-absolute" style="top:5px; right: 5px">
                            <i class="fas fa-home"></i>
                        </div>
                    </a>
                </div>
                
                <div class="card-body">
                    
                    <div class="card-text row">
                        <div class="col-6">
                            <x-field name="name" type="text" label="Nombre" value="{{ $user->name }}"></x-field>
                        </div> 
                        <div class="col-6">
                            <x-field name="email" type="email" value="{{ $user->email }}" help="{{ __('No compartiremos tu correo con terceros.') }}"></x-field>
                        </div>
                    
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary float-end mb-3">{{ __('Actualizar') }}</button>
                </div>
            </x-form>
        </div>

        <div class="card mt-3">
            
                <div class="card-header bg-transparent">
                    {{ count($user->files) }} {{ count($user->files) == 1 ? __('Archivo') : __('Archivos') }}
                </div>
                    <div class="card-body">
                        <div class="card-header bg-transparent">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="select-all" id="select-all">
                                <label class="form-check-label text-select-all" for="select-all">
                                    {{ __("Seleccionar todos") }}
                                </label>
                            </div>
                        </div>
                        
                        <ul class="list-group boder-0 overflow-auto">
                            <x-form method="post" :action="route('files.download')" id="download-form">
                                
                                <input name="user_id" type="hidden" value="{{ $user->id }}"/> 
                                @forelse($user->files as $file)
                                    @switch($file->extension)
                                        @case("jpg")
                                        @case("jepg")
                                        @case("png")
                                        @case("gif")
                                            @php $icon = '<i class="fas fa-file-image fa-2x mr-3 text-info"></i>'; @endphp
                                            @break
                                        @case("doc")
                                        @case("docx")
                                        @php $icon = '<i class="fas fa-file-word fa-2x mr-3 text-primary"></i>'; @endphp
                                            @break
                                        @case("pdf")
                                            @php $icon = '<i class="fas fa-file-pdf fa-2x mr-3 text-danger"></i>'; @endphp
                                            @break
                                        @default
                                            @php $icon = '<i class="fas fa-file fa-2x mr-3 text-black"></i>'; @endphp
                                    @endswitch
                                    
                                    <li class="list-group-item">
                                        <div class="form-check" title="{{ $file->downloaded ? __('Descargado') : '' }}">
                                            <input class="form-check-input" name="download[]" type="checkbox" value="{{ $file->id }}" id="check-{{$file->id}}">
                                            <label class="form-check-label {{ $file->downloaded ? 'text-decoration-line-through text-danger' : '' }}" type="button" for="check-{{$file->id}}">
                                                {!! $icon !!} {!! str_replace($file->extension,"",$file->name) !!}
                                            </label>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-light fw-bold bg-dark">{!! __('No ha subido ningún archivo') !!}</li>
                                @endforelse 
                                
                            </x-form>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <button type="button" class="btn btn-primary float-end" id="send-form">{{ __('Descargar') }}</button>
                    </div>
                
        </div>
    </div>

@endsection

@push('js')
{{-- <script type="text/javascript">
    $('#send-form').on('click', function(){
        $('#download-form').submit();
        blockResubmit();
    })

    $('.list-group-item .form-check-input').on('click', function(){
        
        $('.list-group-item .form-check-input:checkbox').each(function() {
            if(this.checked == true){
                $("#select-all").prop( "checked", true );
                $('.text-select-all').text('{{ __("Borrar selección") }}');
                return false;
            }else{
                $("#select-all").prop( "checked", false );
                $('.text-select-all').text('{{ __("Seleccionar selección") }}');
            }
            
                            
        });
        //$('.text-select-all').text('{{ __("Borrar selección") }}');

    });
    $("#select-all").on('click', function(){
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                $('.text-select-all').text('{{ __("Borrar selección") }}');
                this.checked = true;                        
            });
        } else {
            $(':checkbox').each(function() {
                $('.text-select-all').text('{{ __("Seleccionar todos") }}');
                this.checked = false;                       
            });
        }
    })

    var downloadTimer;
    // Prevents double-submits by waiting for a cookie from the server.
    function blockResubmit() {
        
        downloadTimer = window.setInterval(function () {
            
            var token = Cookies.get("downloadToken");
            
            if (token == 1) {
                unblockSubmit();
                location.reload();
            } else if(attempts == 0){
                alert('error en la descarga');
            }

            attempts--;
        }, 1000);
    }

    function unblockSubmit() {
        window.clearInterval(downloadTimer);
        Cookies.set("downloadToken", 0);
        attempts = 500;
    }
</script> --}}
@endpush