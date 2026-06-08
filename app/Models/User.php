<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the route name that matches the user's role.
     */
    public function redirectRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'penulis' => 'penulis.dashboard',
            'editor' => 'editor.dashboard',
            'layouter' => 'layouter.dashboard',
            default => 'login',
        };
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id_user', 'id_user');
    }

    public function penulis(): HasOne
    {
        return $this->hasOne(Penulis::class, 'id_user', 'id_user');
    }

    public function editor(): HasOne
    {
        return $this->hasOne(Editor::class, 'id_user', 'id_user');
    }

    public function layouter(): HasOne
    {
        return $this->hasOne(Layouter::class, 'id_user', 'id_user');
    }
}
