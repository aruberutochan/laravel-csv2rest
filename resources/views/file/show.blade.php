@extends('layouts.app') 
@section('content') 
<div class="container">
    <div class="row">
        <h2> File Preview </h2>
    </div>
    <table class="table table-striped table-responsive">
        @if(isset($preview))

        <thead>
            <tr>
                @foreach($cols as $colname)
                <th> {{$colname}} </th>

                @endforeach

            </tr>
        </thead>
        <tbody>
            @foreach($preview->toArray() as $row)

            <tr>
                @foreach($row as $rvalue)
                <td>{{ str_limit($rvalue, 30, '...')}}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>


        @endif
    </table>
    @if($errors->any())
        <h4>{{$errors->first()}}</h4>
    @endif 

    {{ Form::open(['route' => 'data.ajaximport' , 'files' => true, 'id' => 'data-ajax-form' ]) }}   

    {!! Form::hidden('uri', $file->uri , []) !!}
    {!! Form::hidden('skip', 0 , []) !!}
    {!! Form::hidden('take', 10 , []) !!}
    {!! Form::hidden('total', $total , []) !!}
    

    <div class="form-group">
        {{ Form::submit( 'Import Data' , ['class' => 'btn btn-success pull-right']) }}
    </div>
    {!! Form::close() !!}

</div>   
        

@endsection


@section('scripts')
<script>

    function savePiece(data, action) {
        console.log(data);
        $.ajax({
            url: action,
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(responseData, textStatus, jqXHR)
            {
                console.log(responseData);
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

    }
    
    $("#data-ajax-form").submit(function (event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        var data = new FormData($("#data-ajax-form")[0]);
        var action = $(this).attr('action');

        savePiece(data, action);
        
    });
</script>
@endsection