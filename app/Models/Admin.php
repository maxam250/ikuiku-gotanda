<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'login_name',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 管理者のロール定数
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';

    /**
     * 利用可能なロール一覧
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'スーパー管理者',
            self::ROLE_ADMIN => '管理者',
        ];
    }

    /**
     * スーパー管理者かどうかを判定
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * 管理者かどうかを判定
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * 認証に使用するフィールド名を指定（セッション用）
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * 認証に使用するフィールドの値を取得（セッション用）
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * 認証時の検索に使用するクエリを指定
     */
    public static function findForPassport($login_name)
    {
        return static::where('login_name', $login_name)->first();
    }
}
