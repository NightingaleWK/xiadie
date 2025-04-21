<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'level',
        'path',
        'is_active',
    ];

    /**
     * 获取当前组织的父组织
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    /**
     * 获取当前组织的所有子组织
     */
    public function children(): HasMany
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    /**
     * 获取当前组织的所有活跃子组织
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * 获取所有祖先组织（从顶层到当前组织的父级）
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ancestors()
    {
        if (!$this->path) {
            return collect();
        }

        $ancestorIds = explode(',', trim($this->path, ','));
        return Organization::whereIn('id', $ancestorIds)->orderBy('level')->get();
    }

    /**
     * 获取所有后代组织
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function descendants()
    {
        return Organization::where('path', 'like', '%,' . $this->id . ',%')->get();
    }

    /**
     * 设置组织的路径和层级
     */
    public function setPathAndLevel()
    {
        if (!$this->parent_id) {
            $this->path = ',';
            $this->level = 0;
        } else {
            $parent = Organization::find($this->parent_id);
            $this->path = $parent->path . $parent->id . ',';
            $this->level = $parent->level + 1;
        }

        return $this;
    }

    /**
     * 创建组织时自动设置路径和层级
     */
    protected static function booted()
    {
        static::creating(function ($organization) {
            $organization->setPathAndLevel();
        });

        static::updating(function ($organization) {
            // 如果父ID变化，需要更新路径和层级
            if ($organization->isDirty('parent_id')) {
                $organization->setPathAndLevel();
            }
        });
    }
}
