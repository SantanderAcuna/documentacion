<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo ItemMenu
 * 
 * Representa los items individuales dentro de menús y submenús
 * Tabla: items_menu
 */
class ItemMenu extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'items_menu';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'submenu_id',
        'menu_id',
        'nombre',
        'slug',
        'url',
        'icono',
        'target',
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
     * Relación con submenú
     *
     * @return BelongsTo
     */
    public function submenu(): BelongsTo
    {
        return $this->belongsTo(SubMenu::class);
    }

    /**
     * Relación con menú
     *
     * @return BelongsTo
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Scope para items activos
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
     * Scope para items de un submenú específico
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $submenuId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorSubmenu($query, $submenuId)
    {
        return $query->where('submenu_id', $submenuId);
    }

    /**
     * Scope para items directos de un menú
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorMenu($query, $menuId)
    {
        return $query->where('menu_id', $menuId)
            ->whereNull('submenu_id');
    }
}
