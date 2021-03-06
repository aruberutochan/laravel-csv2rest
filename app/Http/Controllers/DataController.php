<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data;
use Auth;
use App\MetaData;
use App\Http\Resources\DataResource;
use App\Http\Resources\FieldResource;
use Excel;


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

    public function store(Request $request)
    {           
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx,csv,txt',
        ]);

        $file = Excel::load($request->file('file'), function($excel){
            $sheets = $excel->getSheetNames();
            foreach($sheets as $sheetName) {
                $excel->sheet($sheetName, function($sheet){
                    // modify or something

                });
            }

        });
        foreach($file->get() as $row) {
            $primaryKey = $row->keys()->first();
            $primaryValue = $row->$primaryKey;

            if (! $primaryValue || ! $primaryKey ) {
                return redirect()->back()->withErrors(['It is requiered to have first column as primary so it have to be filled']);
            }
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
