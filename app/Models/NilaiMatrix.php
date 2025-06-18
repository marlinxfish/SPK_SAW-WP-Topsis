<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiMatrix extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alternatif_id',
        'kriteria_id',
        'nilai'
    ];

    /**
     * Get the alternatif that owns the NilaiMatrix
     */
    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }

    /**
     * Get the kriteria that owns the NilaiMatrix
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
