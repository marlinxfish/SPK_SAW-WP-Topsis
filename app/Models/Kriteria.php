<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{

    protected $fillable = ['kode_kriteria', 'nama_kriteria', 'bobot', 'sifat'];

    public function nilaiMatrix()
    {
        return $this->hasMany(NilaiMatrix::class);
    }
}
