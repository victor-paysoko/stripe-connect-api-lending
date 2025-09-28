<?php
// app/Models/ProcessedWebhookEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public $timestamps = false;

    // Scopes
    public function scopeByEventId($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public static function isProcessed(string $eventId): bool
    {
        return static::byEventId($eventId)->exists();
    }

    public static function markAsProcessed(string $eventId, string $type): void
    {
        static::create([
            'event_id' => $eventId,
            'type' => $type,
        ]);
    }
}
