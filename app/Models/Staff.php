<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'role',
        'employment_type',
        'default_start_time',
        'default_end_time',
        'work_days',
        'weekly_schedule',
        'work_start_time',
        'work_end_time',
        'office_id',
        'skills',
        'specialties',
        'experience',
        'is_active',
        'memo',
        'color',
        'sort',
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
     * 所属事務所とのリレーション
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * 事務所名を取得
     */
    public function getOfficeName($officeId)
    {
        if (!$officeId) {
            return null;
        }

        return Office::find($officeId)?->name;
    }

    /**
     * スケジュールとのリレーション
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * メモとのリレーション
     */
    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * レギュレーションとのリレーション
     */
    public function regulrations()
    {
        return $this->hasMany(Regulation::class);
    }

    /**
     * メモ確認状況とのリレーション
     */
    public function memoReads()
    {
        return $this->hasMany(MemoRead::class);
    }

    /**
     * 売掛とのリレーション
     */
    public function accountsReceivables()
    {
        return $this->hasMany(AccountsReceivable::class);
    }

    /**
     * 外注依頼とのリレーション
     */
    public function outsourcingRequests()
    {
        return $this->hasMany(OutsourcingRequest::class, 'contact_staff_id');
    }

    /**
     * PDCAとのリレーション
     */
    public function marketingPdcas()
    {
        return $this->hasMany(MarketingPdca::class);
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

    /**
     * 役職ラベルを取得
     */
    public function getRoleLabelAttribute()
    {
        $roles = [
            'owner' => 'オーナー',
            'admin' => '管理者',
            'manager' => '店長',
            'director' => '管理スタッフ',
            'general' => '一般スタッフ',
            'other' => 'その他',
        ];

        return $roles[$this->role] ?? $this->role;
    }

    /**
     * 雇用形態ラベルを取得
     */
    public function getEmploymentTypeLabelAttribute()
    {
        $types = [
            'full_time' => '正社員',
            'part_time' => 'パート',
            'contract' => '契約社員',
            'freelance' => 'フリーランス',
            'other' => 'その他'
        ];

        return $types[$this->employment_type] ?? $this->employment_type;
    }

    /**
     * 出勤曜日を日本語で取得
     */
    public function getWorkDaysLabelAttribute()
    {
        $dayLabels = [
            '1' => '月',
            '2' => '火',
            '3' => '水',
            '4' => '木',
            '5' => '金',
            '6' => '土',
            '7' => '日'
        ];

        try {
            if ($this->weekly_schedule) {
                $schedule = is_string($this->weekly_schedule) ? json_decode($this->weekly_schedule, true) : $this->weekly_schedule;

                if (is_array($schedule)) {
                    $displayDays = [];
                    foreach ($schedule as $day => $dayInfo) {
                        if (isset($dayLabels[$day]) && isset($dayInfo['is_work']) && $dayInfo['is_work'] == '1') {
                            $displayDays[] = $dayLabels[$day];
                        }
                    }
                    return empty($displayDays) ? '未設定' : implode(', ', $displayDays);
                }
            }

            return '未設定';
        } catch (\Exception $e) {
            return '未設定';
        }
    }

    /**
     * 勤務時間を表示用フォーマットで取得
     */
    public function getWorkTimeLabelAttribute()
    {
        if ($this->work_start_time && $this->work_end_time) {
            return \Carbon\Carbon::parse($this->work_start_time)->format('H:i') . ' - ' .
                   \Carbon\Carbon::parse($this->work_end_time)->format('H:i');
        }

        if ($this->default_start_time && $this->default_end_time) {
            return \Carbon\Carbon::parse($this->default_start_time)->format('H:i') . ' - ' .
                   \Carbon\Carbon::parse($this->default_end_time)->format('H:i');
        }

        return '未設定';
    }
}


