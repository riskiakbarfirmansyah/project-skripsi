<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class narasumbertalkshow extends Model
{
    use HasFactory;

    protected $table = 'narasumber_talkshow';
    protected $primaryKey = 'talkshowID';

    protected $fillable = [
        'nama',
        'instansi',
        'nomor_telp',
        'tema_wawancara',
        'waktu_wawancara',
        'draft_pertanyaan'
    ];
}
