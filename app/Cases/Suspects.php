<?php namespace App\Cases;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Suspects extends Model {

    protected $table = 'suspects';

    protected $fillable = ['name', 'pob', 'dob', 'age', 'religion', 'address', 'city_id', 'nationality', 'job', 'education', 'nama_pimpinan', 'tahanan', 'status'];

    // protected $dates = ['date'];

    public function cases()
    {
        return $this->belongsToMany('App\Cases\Cases');
    }


    public function suspectPob()
    {
        return $this->belongsTo('Eendonesia\Wilayah\Kabupaten', 'pob');
    }
}
