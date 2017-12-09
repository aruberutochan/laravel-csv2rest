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
    {!! Form::hidden('file_id', $file->id , []) !!}
    {!! Form::hidden('uri', $file->uri , []) !!}
    <?php $skip = $file->processed ? $file->processed : 0; ?>
    {!! Form::hidden('skip', $skip , []) !!}
    {!! Form::hidden('take', 200 , []) !!}
    {!! Form::hidden('total', $total , []) !!}
    

    <div class="form-group">
        {{ Form::submit( 'Import Data' , ['class' => 'btn btn-success pull-right']) }}
    </div>
    {!! Form::close() !!}

</div>   
<div class="container">
    <div class="col-md-8 col-md-offset-2">
        <div class="progress aru-progress">
            
            <div class="progress-bar progress-bar-striped active progress-bar-success" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" role="progressbar"  style="width: 0%;">
              <span id="percent-text" class="text-center"> 0%</span>
            </div>
        </div>
    
    </div>
    
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
                if(responseData.finish) {
                    console.log('finish');
                    percent = 100;
                    jQuery('.progress-bar').css({
                        width: percent + '%'
                    });
                    jQuery('#percent-text').text(percent + '%');
                } else {
                    var newData = new FormData($("#data-ajax-form")[0]);
                    newData.set('skip', responseData.skip);
                    var percent = Math.round((parseInt(responseData.skip) / parseInt(responseData.total )) * 10000) / 100;
                    if(percent > 100 ) {
                        percent = 99;
                    }    
                    jQuery('.progress-bar').css({
                        width: percent + '%'
                    });
                    jQuery('#percent-text').text(percent + '%');
                    
                    //data.set('uri', responseData.uri);
                    //data.set('total', responseData.total);
                    //data.set('take', responseData.take);
                    //var action = $(this).attr('action');
                    //data.append('_token', responseData.skip);
                    savePiece(newData, action)
                }
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
        percent = 0.01;
        jQuery('.progress-bar').css({
            width: percent + '%'
        });
        jQuery('#percent-text').text(percent + '%');

        savePiece(data, action);
        
    });
</script>
@endsection