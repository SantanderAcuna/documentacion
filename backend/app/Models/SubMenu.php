<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo SubMenu
 * 
 * Representa los submenús dentro de los menús principales
 * Tabla: submenus
 */
class SubMenu extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'submenus';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'menu_id',
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
     * Relación con menú padre
     *
     * @return BelongsTo
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Relación con items del submenú
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(ItemMenu::class, 'submenu_id');
    }

    /**
     * Scope para submenús activos
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
     * Scope para submenús de un menú específico
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorMenu($query, $menuId)
    {
        return $query->where('menu_id', $menuId);
    }
}
