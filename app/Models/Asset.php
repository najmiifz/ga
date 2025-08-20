<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipe',
        'jenisAset',
        'pic',
        'merk',
        'nomorSn',
        'project',
        'lokasi',
        'tahunBeli',
        'hargaBeli',
        'hargaSewa',
    ];

    /**
     * Mendapatkan data pajak yang berelasi dengan aset.
     */
    public function pajak()
    {
        return $this->hasOne(Pajak::class);
    }

    /**
     * Mendapatkan semua riwayat servis untuk aset ini.
     */
    public function servis()
    {
        return $this->hasMany(Servis::class);
    }

    /**
     * Mendapatkan semua jadwal maintenance untuk aset ini.
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
