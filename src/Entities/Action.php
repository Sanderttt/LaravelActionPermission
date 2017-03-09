<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 15:45
 */

namespace RobinVanDijk\LaravelActionPermission\Entities;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{

    protected $table = 'controller_actions';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'controller',
        'function',
        'method',
        'name',
        'path',
        'is_ignored',
        'in_nav',
        'group',
        'alias'
    ];

    protected $casts = ['is_ignored' => 'boolean', 'in_nav' => 'boolean'];

}
