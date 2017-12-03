@extends('layouts.app') 

@section('content')




<div class="container">   
@if($errors->any())
    <h4>{{$errors->first()}}</h4>
@endif 
       
    {{ Form::open(['route' => 'data.store' , 'files' => true ]) }}     
    {{Form::token()}}
    <div class="form-group">
        {{ Form::label('Title', 'title') }} 
        {{ Form::text('title',null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
        {{ Form::label('File', 'file') }} 
        {{ Form::file('file',null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group">
        {{ Form::submit( 'Save' , ['class' => 'btn btn-success pull-right']) }}
    </div>
    {!! Form::close() !!}


</div>

@endsection