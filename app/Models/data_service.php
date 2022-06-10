<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'kd_buku',
        'judul_buku',
        'jumlah',
        'tanggal',
        'action',
        'status',
    ];
}
