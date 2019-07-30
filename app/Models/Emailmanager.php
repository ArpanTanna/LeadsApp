<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Emailmanager extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emailmanager';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'lead_ids'
    ];

    /**
     * Scope for select query
     *
     */
    public function scopeEi($query,$exclude = array(), $include = array())
    {
        return $query->select( array_diff( array_merge($this->fillable, $include),(array) $exclude) );
    }

    /**
     * Get the user associated with lead.
     *
     * pt=path_to
     */
    public function ptuser()
    {
        return $this->belongsTo("App\User", 'user_id', 'id');
    }

}
