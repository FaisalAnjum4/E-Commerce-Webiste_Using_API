<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenSellerModel extends Model
{
    use HasFactory;
    protected $table = "tokenseller";
    public $timestamps=false;
    protected $primaryKey="t_id";
}
