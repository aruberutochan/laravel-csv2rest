@extends('layouts.app') 
@section('content') 
    @if(isset($model))
        <div class="container">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="text-center">
                            @if(isset($model->name)) {{$model->name}} @elseif(isset($model->title)) {{$model->title}} @endif
                        </h2>
                        <dl class="dl-horizontal">
                            <dt>Model Type</dt>
                            <dd> {{class_basename(get_class($model))}}</dd>
                            @foreach($model->getAttributes() as $field => $value)
                            <dt>{{$field}}</dt>
                            <dd> {{$value}}</dd>
                            @endforeach
                        </dl>
                    </div>
                    <div class="panel-footer">
                        <div class="pull-right">
                            <a href="{{Request::url()}}/edit" class="btn btn-success btn">Edit</a>
                            <a href="#delete" class="btn btn-danger btn">Delete</a>                          
                        </div>
                        <div class="clearfix"></div>
                             
                    </div>
                </div>
            </div>
        </div>
    @else nope     
    @endif 
@endsection