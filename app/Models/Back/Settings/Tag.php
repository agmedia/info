<?php

namespace App\Models\Back\Settings;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Tag extends Model
{

    /**
     * @var string
     */
    protected $table = 'page_tags';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
