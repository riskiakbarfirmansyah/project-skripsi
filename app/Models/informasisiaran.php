<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class informasisiaran extends Model
{
    use HasFactory;

    protected $table = 'informasi_siarans';
    protected $primaryKey = 'siaranID';

    protected $fillable = [
        'siaranID',
        'tanggal',
        'waktu',
        'keterangan',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];
}
