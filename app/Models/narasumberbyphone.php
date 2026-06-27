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
        'user_id',
        'nama',
        'instansi',
        'nomor_telp',
        'tema_wawancara',
        'waktu_wawancara',
        'byphone_schedule',
        'draft_pertanyaan',
        'is_self_registered',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

