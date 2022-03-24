@component('mail::message')
# Hola {{ config('app.name') }}
<br>
Soy {{$user->name }} y he subido {{ $count_files }} {{ $count_files == 1 ? 'archivo' : 'archivos'}} a la plataforma de {{ config('app.name') }}.
<br>

@component('mail::button', [
    'url' => env("APP_URL")
])
    Ir a la plataforma
@endcomponent

@endcomponent