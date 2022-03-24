@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" >
   
    <style>
        .card-body > .list-group{
            max-height: 300px;
        }
    </style>

@endpush

@section('content')
    <div class="container mb-3">
        <div class="text-center">
            <h2>Subir documentos</h2> 
        </div>
    </div>

    <div class="container">
        <div class="row">

            </div>        
        <form method="post" action="{{ route('files.store') }}" enctype="multipart/form-data"
            class="dropzone" id="dropzone">
            @csrf
            </form>
        </div>
        @if(Auth()->user()->role == 1)
            <div class="row mt-3">
                <div class="col-lg-12 margin-tb">
                    <div class="text-center">
                        <a class="btn btn-success" href="{{ route('home') }}" title="return to index"> <i class="fas fa-backward fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="container">
            <div class="card mt-3">
            
                <div class="card-header bg-transparent">
                    {{ count(Auth()->user()->files) }} {{ count(Auth()->user()->files) == 1 ? __('Archivo') : __('Archivos') }}
                    <a href="{{ route('files.send') }}" class="text-decoration-none">
                        <div class="btn btn-dark position-absolute" style="right:5px; top:5px" title="Notificar subida de archivos">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </a>
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
                            <x-form method="post" :action="route('files.destroy')" id="download-form">
                                
                                <input name="user_id" type="hidden" value="{{ Auth()->id() }}"/> 
                                @forelse(Auth()->user()->files as $file)
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
                                        <div class="form-check" title="{{ $file->notificated ? __('Subida notificada') : '' }}">
                                            <input class="form-check-input" name="download[]" type="checkbox" value="{{ $file->id }}" id="check-{{$file->id}}">
                                            <label class="form-check-label {{ $file->notificated ? 'text-decoration-line-through text-danger' : '' }}" type="button" for="check-{{$file->id}}">
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
                        <button type="button" class="btn btn-primary float-end" id="send-form">{{ __('Eliminar') }}</button>
                    </div>
                
        </div>
        </div>
@endsection
@push('js')
    
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script type="text/javascript">
        Dropzone.options.dropzone =
        {
            maxFilesize: 12,
            resizeQuality: 1.0,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx",
            addRemoveLinks: true,
            timeout: 60000,
            removedfile: function(file) 
            {
                var name = file.upload.filename;
                $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                    type: 'POST',
                    url: '{{ url("files/destroy") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {filename: name},
                    success: function (data){
                        console.log(data);
                        console.log("File has been successfully removed!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function (file, response) {
                console.log(response);
            },
            error: function (file, response) {
                return false;
            }
        };
    </script>
@endpush