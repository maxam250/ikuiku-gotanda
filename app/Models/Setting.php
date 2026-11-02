<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'square_payment_url',
        'fanclub_join_terms',
        'payment_description',
        'specified_commercial_transactions',
        'fanclub_withdraw_message',
        'pricing_type',
        'tinymce_skin',
        'schedule_switch_hour',
    ];
}
