<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternatif extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_alternatif',
        'nama_alternatif'
    ];

    public function nilaiMatrices()
    {
        return $this->hasMany(NilaiMatrix::class);
    }
}
