@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#collapse1">Raw Models</a>
            </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
            <div class="panel-body">
                <pre>
                    @php print_r($models) @endphp
                </pre>
            
            </div>
            </div>
        </div>
    </div> 
    <table class="table table-striped table-responsive">
        @if(isset($models[0]))
            <thead>
                <tr>
                    @foreach($models[0]->getAttributes() as $field => $value)
                        @if(is_array($value))
                            @foreach($value as $f => $v)
                            <th> {{$f}} </th>
                            @endforeach
                        @else
                            <th>{{$field}}</th>
                        @endif
                    @endforeach
                    @if(isset($baseRoute))
                        <th>Actions</th>
                        <th> </th>
                        <th></th>
                    @endif
                </tr>    
            </thead>
            <tbody>
                @foreach($models as $model)
                    <tr> 
                        @foreach($model->getAttributes() as $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <td>{{ str_limit($v, 30, '...')}}</td>
                                @endforeach
                            @else
                                <td>{{str_limit($value, 30, '...')}}</td>
                            @endif 
                        @endforeach 
                        @if(isset($baseRoute))
                            <td> <a href="{{Request::url()}}/{{$model->id}}" class="btn btn-info btn-xs">View</a></td>
                            <td> <a href="/{{ $baseRoute }}/{{$model->id}}/edit" class="btn btn-success btn-xs">Edit</a></td>
                            <td>
                                {{ Form::open(array('route' => ['data.destroy', $model], 'method' => 'delete')) }}   

                                {{Form::token()}}
                                {{Form::button('<span class="ion-ios-trash"></span> Delete', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs']) }}
                            
                                {{ Form::close() }} 
                            </td>
                        @endif       
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>
@endsection