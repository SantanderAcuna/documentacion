<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pqrs extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'folio',
        'tipo',
        'nombre',
        'email',
        'telefono',
        'documento',
        'asunto',
        'mensaje',
        'estado',
        'respuesta',
        'respondido_at',
        'respondido_por',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'respondido_at' => 'datetime',
    ];

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['estado', 'respuesta', 'respondido_at'])
            ->logOnlyDirty();
    }

    /**
     * Get the user who responded to the PQRS.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    /**
     * Scope a query to only include new PQRS.
     */
    public function scopeNew($query)
    {
        return $query->where('estado', 'nuevo');
    }

    /**
     * Scope a query to only include in-process PQRS.
     */
    public function scopeInProcess($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('tipo', $type);
    }

    /**
     * Generate a unique folio for the PQRS.
     */
    public static function generateFolio(): string
    {
        return 'PQRS-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }
}
