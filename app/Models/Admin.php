<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $table = 'admin';

    protected $primaryKey = 'id_admin';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'email',
        'username',
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
