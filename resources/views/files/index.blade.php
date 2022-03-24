@extends('layouts.app')

@push('css')
    <style>
        .card:hover {
           border: 1px solid red;
           filter: grayscale(100%);
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-lg-12 margin-tb">
            <div class="text-center">
                <h2>Listado de archivos subidos por clientes</h2>
                   {{--  <a class="btn btn-success" href="{{ route('files.upload') }}" title="Upload files"> 
                        <i class="fas fa-upload fa-2x"></i>
                    </a> --}}
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="container">
        <x-form method="post" :action="route('home')" id="download-form">
            <div class="input-group mb-3">
                <div type="button" class="input-group-text" id="newUser" data-bs-toggle="modal" data-bs-target="#register-modal"><i class="fas fa-user-plus"></i></div>
                <input name="search" class="form-control" type="text" />
                <button class="input-group-text" id="search" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </x-form>
    </div>
    <div class="container">
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-0">
            @forelse ($users as $user)
                <div class="col mb-3">
                    <a href="{{ route('users.user',$user) }}" class="text-decoration-none">
                        <div class="card">
                            <img class="card-img-top"  src="/storage/files.png" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">{{ $user->name }}</h5>
                                <p class="card-text">Tiene {{ $user->files_count }} {{ $user->files_count == 1 ? 'archivo' : 'archivos' }}</p>
                            </div>
                    </a>
                            <div class="card-footer">
                                <small class="text-muted">{{ $user->files_count ? $user->files->last()->created_at->diffForHumans() : __('Sin archivos') }}</small>
                            </div>
                        </div>
                </div>
            @empty
                
                    <div class="bg-danger">
                        {!! __("Ningún resultado") !!}
                    </div>
                        
            @endforelse
            
        </div>
        <div class="d-flex justify-content-center">
            {!! $users->links() !!}
        </div>
    </div>
    
@endsection
@include('modals.formUser')

@push('js')
    <script>
        
        @if(session('error-register'))
            $("#register-modal").modal();
        @endif
    </script>
@endpush