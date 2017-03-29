<?php

namespace CenarioWeb\Model;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table      = 'postman_email';
    public    $timestamps = false;

    protected $fillable = [
        'key', 'from', 'to', 'subject',
        'date', 'vendor', 'vendor_response',
        'vendor_status', 'vendor_message_id',
        'vendor_error'
    ];
}
