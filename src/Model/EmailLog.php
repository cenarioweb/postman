<?php

namespace CenarioWeb\Model;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table      = 'postman_email_log';
    public    $timestamps = false;

    protected $fillable = [
        'email_id', 'date', 'event', 'url'
    ];
}
