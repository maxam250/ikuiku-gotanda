<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalStaff extends Model
{
    use HasFactory;

    // 外部サーバーのデータベース接続を指定
    protected $connection = 'external_mysql';

    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'sort',
        'role',
        'employment_type',
        'default_start_time',
        'default_end_time',
        'work_days',
        'weekly_schedule',
        'work_start_time',
        'work_end_time',
        'default_studio_id',
        'skills',
        'specialties',
        'experience',
        'is_active',
        'memo',
        'color',
        'auto_schedule_enabled',
        'auto_schedule_start_date',
        'auto_schedule_end_date',
    ];

    protected $casts = [
        'work_days' => 'array',
        'weekly_schedule' => 'array',
        'skills' => 'array',
        'default_start_time' => 'datetime',
        'default_end_time' => 'datetime',
        'work_start_time' => 'datetime',
        'work_end_time' => 'datetime',
        'is_active' => 'boolean',
        'auto_schedule_enabled' => 'boolean',
        'auto_schedule_start_date' => 'date',
        'auto_schedule_end_date' => 'date',
    ];

    /**
     * 予約とのリレーション
     */
    public function reservations()
    {
        return $this->hasMany(ExternalReservation::class);
    }

    /**
     * 役職のテキストを取得
     */
    public function getRoleTextAttribute()
    {
        $roles = [
            'owner' => 'オーナー',
            'admin' => '管理者',
            'manager' => '店長',
            'director' => '管理スタッフ',
            'general' => '一般スタッフ',
            'other' => 'その他',
        ];

        return $roles[$this->role] ?? '不明';
    }

    /**
     * 雇用形態のテキストを取得
     */
    public function getEmploymentTypeTextAttribute()
    {
        $types = [
            'full_time' => '正社員',
            'part_time' => 'パート',
            'contract' => '契約社員',
            'freelance' => 'フリーランス',
            'other' => 'その他',
        ];

        return $types[$this->employment_type] ?? '不明';
    }

    /**
     * アクティブなスタッフのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 役職別で取得
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

}
