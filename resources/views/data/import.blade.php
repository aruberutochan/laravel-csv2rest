@extends('layouts.app') 

@section('content')




<div class="container">   
@if($errors->any())
    <h4>{{$errors->first()}}</h4>
@endif 
       
    {{ Form::open(['route' => 'data.ajax' , 'files' => true, 'id' => 'data-ajax-form' ]) }}     

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

    <button class="btn btn-success" id="btn-save"> Ajax </button>
</div>

@endsection


@section('scripts')
<script>
    
    $("#data-ajax-form").submit(function (event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE

        // Create a formdata object and add the files
        var data = new FormData();

        var token = $('meta[name=csrf-token]').attr('content');
        // data.append('_token', token);
        // console.log(data);

        $.ajax({
            url: '/data/ajax',
            type: 'POST',
            data: new FormData($("#data-ajax-form")[0]),
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
                console.log(data);
                //console.log(textStatus);
                //console.log(jqXHR);
                if(typeof data.error === 'undefined')
                {
                    // // Success so call function to process the form
                    // submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            }
        });
    });
</script>
@endsection