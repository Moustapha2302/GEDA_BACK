<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['code', 'nom', 'responsable_poste'];

    public function actes()
    {
        return $this->hasMany(Acte::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
