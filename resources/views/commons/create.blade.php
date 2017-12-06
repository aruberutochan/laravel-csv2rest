@extends('layouts.app') 

@section('content')

<div class="container">

    @if(!$model->get()->isEmpty()) 
        <h2> Edit @if(isset($model->name)) {{$model->name}} @elseif(isset($model->title)) {{$model->title}} @endif </h2>
        {{ Form::model($model, ['route' => [$route, $model], 'method' => 'post', 'files' => true]) }}     
    @else
        <h2> Create @if(isset($model->name)) {{$model->name}} @elseif(isset($model->title)) {{$model->title}} @endif </h2>
        
       
    {{ Form::open(['route' => $route , 'files' => true ]) }}  
    @endif 
    
    {{Form::token()}}

    @foreach($fields as $field)
        @if($field['type'] == 'text')
            <div class="form-group">
                {{ Form::label($field['field'], $field['name']) }} 
                {{ Form::text($field['field'],null, ['class' => 'form-control']) }}
            </div>
        @elseif($field['type'] == 'number')
            <div class="form-group">
                {{ Form::label($field['field'], $field['name']) }} 
                {{ Form::number($field['field'],null, ['class' => 'form-control']) }}
            </div>
        @elseif($field['type'] == 'file')
            <div class="form-group">
                {{ Form::label($field['field'], $field['name']) }} 
                {{ Form::file($field['field'],null, ['class' => 'form-control']) }}
            </div>
        @elseif($field['type'] == 'textarea')
            <div class="form-group">
                {{ Form::label($field['field'], $field['name']) }} 
                {{ Form::textarea($field['field'],null, ['class' => 'form-control', 'rows' => 4]) }}
            </div>
        
        @elseif($field['type'] == 'map')
            <div class="form-group">
                {{ Form::label('longitude', $field['name'] . ' longitude') }} 

                {{ Form::number('longitude',null, ['class' => 'form-control', 'step' => 0.0000000000001]) }}
            </div>
            <div class="form-group">
                {{ Form::label('lattitude', $field['name'] . ' lattitude') }} 
                {{ Form::number('lattitude',null, ['class' => 'form-control', 'step' => 0.0000000000001]) }}
            </div>
        @endif


    @endforeach
    <div class="form-group">
        {{ Form::submit( 'Save ' . class_basename(get_class($model)) , ['class' => 'btn btn-success pull-right']) }}
    </div>



    {!! Form::close() !!}


</div>

@endsection