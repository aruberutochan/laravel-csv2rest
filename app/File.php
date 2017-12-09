<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class File extends Model
{
    protected $fillable = [
        'name', 'uri', 'processed'
    ];
    public $fields = [
        [   
            'name' => 'File Name',
            'field' => 'name',
            'type' => 'text',
            'required' => true,       
        ],
        [
            'name' => 'File',
            'field' => 'file',
            'type' => 'file',
            'required' => true,       
        ], 
                
    ];
    public function user() {
        return $this->belongsTo('App\User');
    }
}
