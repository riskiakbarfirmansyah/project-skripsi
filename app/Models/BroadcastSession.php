<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastSession extends Model
{
    use HasFactory;

    protected $table = 'broadcast_sessions';

    protected $fillable = [
        'penyiar_id',
        'narasumber_type',
        'narasumber_id',
        'started_at',
        'ended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function penyiar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyiar_id');
    }

    public function narasumber()
    {
        if ($this->narasumber_type === 'byphone') {
            return $this->belongsTo(narasumberbyphone::class, 'narasumber_id', 'byphoneID');
        }
        return $this->belongsTo(narasumbertalkshow::class, 'narasumber_id', 'talkshowID');
    }

    public function getNarasumberAttribute()
    {
        if ($this->narasumber_type === 'byphone') {
            return narasumberbyphone::find($this->narasumber_id);
        }
        return narasumbertalkshow::find($this->narasumber_id);
    }

    public function getDurationInSecondsAttribute(): int
    {
        if (! $this->started_at) {
            return 0;
        }
        $end = $this->ended_at ?? now();
        return (int) $this->started_at->diffInSeconds($end);
    }
}
