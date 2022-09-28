<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTokenModel extends Model
{
    use HasFactory;
    protected $table = "tokenadmin";
    public $timestamps=false;
    protected $primaryKey="t_id";
}
