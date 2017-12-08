@extends('layouts.app')

@section('content')
<div class="flex-center">
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h1>AGGA scripts</h1>
            </div>
        </div>
        <div class="row">
            <div class="text-center">
                @auth
                    <a class="btn btn-primary" href="{{ url('/data') }}">Ver Datos</a>
                    <a class="btn btn-primary" href="{{ url('/file') }}">Subir Archivos</a>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Acceder</a>
                    <a class="btn btn-primary" href="{{ route('register') }}"> Registrarse </a>
                @endauth
            </div>
        
        </div>
        

        
    </div>
</div>

@endsection
