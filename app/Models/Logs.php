<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'user_uid',
        'email',
        'email_send_to',
        'service',
        'subject',
        'body',
        'status',
    ];
}
