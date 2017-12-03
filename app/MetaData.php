<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    protected $fillable = [
        'key', 'value'
    ];
    public $table = "meta_datas";
    protected $visible = [
        'key', 'value',
    ];
    public function data() {
        return $this->belongsTo('App\Data');
    }
}
