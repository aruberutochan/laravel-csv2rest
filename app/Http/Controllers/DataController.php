<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data;
use Auth;
use App\MetaData;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\DataResource;
use App\Http\Resources\FieldResource;
use Excel;
use App\File;

class DataController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {       
        // $datas = Data::WithLocation()->get();
        
        $datas = Data::with('metaDatas')->paginate(15);
        $queries = $request->all();
        $modelData = new Data();
        $primaryKeys = $modelData->primaryKeys()->get();
        if($request->operator == 'equal') {
            $operator = '=';
            $operatorChar = '';
        } else {
            $operator = 'LIKE';
            $operatorChar = '%';
        }

        $search = Data::with('metaDatas')->where(function($query) use ($request, $primaryKeys, $operator, $operatorChar)
        {   
            foreach($request->all() as $key => $value){
                if($key != 'page' && $key != 'operator'){
                    if($primaryKeys->where('primary_key', $key )->first()) {                       
                        $query = $query->where([
                            ['primary_key' , '=', $key],
                            ['primary_value', $operator, $operatorChar . $value . $operatorChar],
                        ]);
                    } else {

                        $query->whereHas('metaDatas', function($query) use ($request, $key, $value, $operator, $operatorChar) {
                            $query->where([
                                ['key', '=', $key],
                                ['value', $operator, $operatorChar . $value . $operatorChar],
                            ]);
                        });
                    }                    
                }                
            }           
            
        })->paginate(15);
        
        if ($request->route()->getPrefix() == 'api') {
            return new DataResource($search);
        } else {
            return view('data.index', [
                'models' => $search,
                'baseRoute' => 'data'
            ]);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {     
        return view('data.import');   
        
    }

    public function ajaxImport(Request $request) {
        
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'uri' => 'required',
            'skip' => 'required',
            'take' => 'required',
            'total' => 'required'            
        ]);
        $user = Auth::user();
        
        
        if ($request->skip > $request->total) {
            return response()->json(['finish' => true]);  
        } else {
            $results = Excel::load(Storage::url('app/'. $request->uri))->skipRows($request->skip)->takeRows($request->take);
            foreach ($results->toArray() as $row) {  
                    
                if(!isset($primaryKey)){
                    
                    $primaryKey = key($row);  
                }                  
                $primaryValue = $row[$primaryKey];
    
                if ( $primaryValue ) {
                
                    $data = $user->datas()->updateOrCreate([
                        'primary_key' => $primaryKey,
                        'primary_value' => $primaryValue,
                    ]);
                    
                    unset($row[$primaryKey]);
                    $metas = array();
                    foreach($row as $colName => $colValue){
                        if( $colValue ) {
                            $metas[] = new MetaData(['key' => $colName, 'value' => $colValue]);
                        }                
                    }
    
                    $data->metaDatas()->saveMany( $metas );
                }
            }
            
            return response()->json(['skip' => $request->skip + $request->take, 'total' => $request->total, 'uri' => $request->uri]) ;
            
        }
                
    }

    /**
    * Mass (bulk) insert or update on duplicate for Laravel 4/5
    * 
    * insertOrUpdate([
    *   ['id'=>1,'value'=>10],
    *   ['id'=>2,'value'=>60]
    * ]);
    * 
    *
    * @param array $rows
    */
    protected function insertOrUpdate(array $rows){
        $table = \DB::getTablePrefix().with(new self)->getTable();


        $first = reset($rows);

        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );

        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                    array_map( function( $value ) { return '"'.str_replace('"', '""', $value).'"'; } , $row )
                ).')';
            } , $rows )
        );

        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );

        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        return \DB::statement( $sql );
    }


    public function ajaxImportSlow(Request $request) {
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'file_id' => 'required',
            'uri' => 'required',
            'skip' => 'required',
            'take' => 'required',
            'total' => 'required'            
        ]);
        
        if ($request->skip > $request->total) {
            return response()->json(['finish' => true]);  
        } else {
            $results = Excel::load(Storage::url('app/'. $request->uri))->skipRows($request->skip)->takeRows($request->take);
            foreach ($results->get() as $row) {        
                
                $primaryKey = $row->keys()->first();     
                $primaryValue = $row->$primaryKey;
    
                if ( $primaryValue && $primaryKey ) {
                
                    $data = Auth::user()->datas()->updateOrCreate([
                        'primary_key' => $primaryKey,
                        'primary_value' => $primaryValue,
                    ]);
                    
                    $metas = array();
    
                    foreach($row as $colName => $colValue){
                        if($colName != $primaryKey && $colValue ) {
                            $metas[] = new MetaData(['key' => $colName, 'value' => $colValue]);
                        }                
                    }
    
                    $data->metaDatas()->saveMany( $metas );
                }
            }
            $file = new File();
            $file->processed = $request->skip + $request->take;
            $file->save();
            return response()->json(['skip' => $request->skip + $request->take, 'total' => $request->total, 'uri' => $request->uri]) ;
            
        }

                
    }

    public function ajaxtest(Request $request) {
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'file' => 'required',            
            'title' => 'required',
        ]);
        
        if($request->hasFile('file')) {
            $file = Excel::load($request->file('file'));
            $title = preg_replace("/[^a-z0-9\.]/", "_", strtolower($request->title));
            $name = $title . '-' . time();
            $file->setFileName($name); 
            $csvSave = $file->store('csv', storage_path('app/import/'), true);
            return response()->json($csvSave) ;
        } else {
            return response()->json($request) ;
        }
        
    }

    public function store(Request $request)
    {           
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'file' => 'required',            
            'title' => 'required',
        ]);
        $file = Excel::load($request->file('file'));
        $title = preg_replace("/[^a-z0-9\.]/", "_", strtolower($request->title));
        $name = $title . '-' . time();
        $file->setFileName($name); 
        $csvSave = $file->store('csv', storage_path('app/import/'), true);
        Excel::filter('chunk')->load($csvSave['full'])->chunk(50, function($results) {
            foreach ($results as $row) {
                $primaryKey = $row->keys()->first();
                $primaryValue = $row->$primaryKey;
    
                if ( $primaryValue && $primaryKey ) {
                
                    $data = Auth::user()->datas()->updateOrCreate([
                        'primary_key' => $primaryKey,
                        'primary_value' => $primaryValue,
                    ]);
                    
                    $metas = array();
    
                    foreach($row as $colName => $colValue){
                        if($colName != $primaryKey ) {
                            $metas[] = new MetaData(['key' => $colName, 'value' => $colValue]);
                        }                
                    }
    
                    $data->metaDatas()->saveMany( $metas );
                }
            }
        });

        return redirect()->route('data.index');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Data $data)
    {
       $data->load('metaDatas');
        
        if ($request->route()->getPrefix() == 'api') {
            return new DataResource($data);
        } else {
            return view('data.show', ['model' => $data]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Data $data = null)
    {
        if($data->delete()) {
            $apiResponse = response('The data has been deleted successfully', 200);
        } else {
            $apiResponse = response('There were some problems and data has not been deleted', 404);
        }
        if ($request->route()->getPrefix() == 'api') {
            return $apiResponse;
        } else {
            return redirect()->route('data.index');;
        }
        
    }
}
