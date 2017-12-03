<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\MetaData;
class Data extends Model
{
    protected $fillable = [
        'primary_key', 'primary_value'
    ];
    public $table = "data";
    protected $visible = [
        'primary_key', 'primary_value', 'metaDatas'
    ];
    public function user() {
        return $this->belongsTo('App\User');
    }

    public function metaDatas() {
        return $this->hasMany('App\MetaData');
    }

    public function primaryKeys() {
        return $this->select('primary_key')->distinct();
    }
}
