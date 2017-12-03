@extends('layouts.app') 
@section('content') 
    @if(isset($model))
        <div class="container">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="text-center">
                            @if(isset($model->primary_value)) {{$model->primary_key}} : {{$model->primary_value}} @endif
                        </h2>
                        <dl class="dl-horizontal">
                            <dt>Model Type</dt>
                            <dd> {{class_basename(get_class($model))}}</dd>
                            @foreach($model->getVisible() as $field)
                                @if(!is_array($model->$field) && ! is_object($model->$field))
                                <dt>{{$field}}</dt>
                                <dd> {{$model->$field}}</dd>
                                @endif
                            @endforeach
                            @foreach($model->metaDatas as $metaData)
                            <dt>{{$metaData->key}}</dt>
                            <dd> {{$metaData->value}}</dd>
                            @endforeach
                        </dl>
                    </div>
                    <div class="panel-footer">
                        <div class="pull-right">
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