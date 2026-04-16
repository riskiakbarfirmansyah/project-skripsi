<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class narasumberbyphone extends Model
{
    use HasFactory;

    protected $table = 'narasumber_byphones';
    protected $primaryKey = 'byphoneID';

    protected $fillable = [
        'nama',
        'instansi',
        'nomor_telp',
        'tema_wawancara',
        'waktu_wawancara',
        'draft_pertanyaan',
        'is_self_registered',
    ];
}
