<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerModel extends Model
{
    use HasFactory;
    protected $table = "sellers";
    public $timestamps=false;
    protected $primaryKey="s_id";
}
