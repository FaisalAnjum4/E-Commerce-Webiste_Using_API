<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmOrderModel extends Model
{
    protected $table = "confirm_order";
    //public $timestamps=false;
    protected $primaryKey="CO_id";
}
