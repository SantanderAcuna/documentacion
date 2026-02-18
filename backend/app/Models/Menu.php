<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Menu
 * 
 * Representa los menús principales del sistema
 * Tabla: menus
 */
class Menu extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'icono',
        'orden',
        'esta_activo',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'orden' => 'integer',
        'esta_activo' => 'boolean',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con submenús
     *
     * @return HasMany
     */
    public function submenus(): HasMany
    {
        return $this->hasMany(SubMenu::class);
    }

    /**
     * Relación con items de menú directos
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(ItemMenu::class);
    }

    /**
     * Scope para menús activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para ordenar por orden establecido
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    /**
     * Scope para obtener menú con submenús e items
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConSubmenusEItems($query)
    {
        return $query->with(['submenus.items', 'items']);
    }
}
