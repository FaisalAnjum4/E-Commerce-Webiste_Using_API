<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeModel extends Model
{
    protected $table = "notice";
    public $timestamps=false;
    protected $primaryKey="n_id";
}
