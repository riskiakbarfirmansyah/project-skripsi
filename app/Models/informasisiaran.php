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
    ];
}
