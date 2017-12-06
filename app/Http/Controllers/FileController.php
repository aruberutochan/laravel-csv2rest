<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Excel;
use Auth;
use App\File;
class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $farms = Farm::WithLocation()->get();
        
        $files = File::paginate(15);
        
        if ($request->route()->getPrefix() == 'api') {
            return new DataResource($files);
        } else {
            return view('file.index', [
                'models' => $files,
                'baseRoute' => 'file'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $file = new File();   
        $fields = $file->fields;

        return view('commons.create', [
            'fields' => $fields, 
            'route' => 'file.store', 
            'model' => $file,
            
        ]);
              
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'file' => 'required',            
            'name' => 'required',
        ]);
        $file = Excel::load($request->file('file'));
        $title = preg_replace("/[^a-z0-9\.]/", "_", strtolower($request->name));
        $name = $title . '-' . time();
        $file->setFileName($name); 
        $csvSave = $file->store('csv', storage_path('app/files/'), true);
        //dd($csvSave);
        if($csvSave){
            $fileDB = Auth::user()->files()->updateOrCreate([
                'name' => $name,
                'uri' => 'files/' . $csvSave['file'],
            ]);
        }

        return redirect()->route('file.index');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, File $file)
    {
        
        $excel = Excel::load( Storage::url('app/'. $file->uri));
        $total = $excel->getTotalRowsOfFile() - 1; //->count();
        $preview = $excel->takeRows(10);        
        $cols = $excel->first()->keys()->toArray();
        //dd($excel);
        
        return view('file.show', ['file' => $file, 'excel' => $excel, 'preview' => $preview , 'cols' => $cols, 'total' => $total]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, File $file)
    {

        return view('commons.create', [
            'fields' => $file->fields, 
            'route' => 'file.update', 
            'model' => $file,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        $this->validate($request, [
            //'file' => 'required|mimes:xls,xlsx,csv,txt,text/csv',
            'file' => 'required',            
            'name' => 'required',
        ]);
        $newFile = Excel::load($request->file('file'));
        $title = preg_replace("/[^a-z0-9\.]/", "_", strtolower($request->name));
        $name = $title . '-' . time();
        $newFile->setFileName($name); 
        $csvSave = $newFile->store('csv', storage_path('app/files/'), true);
        if($csvSave){
            $file->update([
                'name' => $name,
                'uri' => 'files/' . $name,
            ]);
        }

        return redirect()->route('file.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $storage = Storage::delete($file->uri);
        $file->delete();
        return redirect()->route('file.index');      

    }
}
