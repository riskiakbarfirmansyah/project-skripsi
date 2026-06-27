<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class narasumbertalkshow extends Model
{
    use HasFactory;

    protected $table = 'narasumber_talkshows';
    protected $primaryKey = 'talkshowID';

    protected $fillable = [
        'user_id',
        'nama',
        'instansi',
        'nomor_telp',
        'tema_wawancara',
        'waktu_wawancara',
        'talkshow_schedule',
        'draft_pertanyaan',
        'is_self_registered',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

