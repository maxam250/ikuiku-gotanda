<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    private function isSuperAdmin(): bool
    {
        return auth()->guard('admin')->user()->isSuperAdmin();
    }

    /**
     * 管理者一覧を表示
     */
    public function index()
    {
        $query = Admin::orderBy('created_at', 'desc');
        if (!$this->isSuperAdmin()) {
            $query->where('role', '!=', Admin::ROLE_SUPER_ADMIN);
        }
        $admins = $query->paginate(20);
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * 管理者作成フォームを表示
     */
    public function create()
    {
        $roles = $this->isSuperAdmin() ? Admin::getRoles() : [Admin::ROLE_ADMIN => 'admin'];
        return view('admin.admins.create', compact('roles'));
    }

    /**
     * 新しい管理者を作成
     */
    public function store(Request $request)
    {
        $allowedRoles = $this->isSuperAdmin()
            ? array_keys(Admin::getRoles())
            : [Admin::ROLE_ADMIN];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'login_name' => 'required|string|max:255|unique:admins,login_name',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'status' => 'required|boolean',
        ], [
            'name.required' => '名前は必須です。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'login_name.required' => 'ログイン名は必須です。',
            'login_name.unique' => 'このログイン名は既に使用されています。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードの確認が一致しません。',
            'role.required' => 'ロールは必須です。',
            'role.in' => '無効なロールが選択されています。',
            'status.required' => 'ステータスは必須です。',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'login_name' => $request->login_name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', '管理者が正常に作成されました。');
    }

    /**
     * 管理者編集フォームを表示
     */
    public function edit(Admin $admin)
    {
        if (!$this->isSuperAdmin() && $admin->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'スーパー管理者の情報は編集できません。');
        }
        $roles = $this->isSuperAdmin() ? Admin::getRoles() : [Admin::ROLE_ADMIN => 'admin'];
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * 管理者情報を更新
     */
    public function update(Request $request, Admin $admin)
    {
        if (!$this->isSuperAdmin() && $admin->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'スーパー管理者の情報は更新できません。');
        }

        $allowedRoles = $this->isSuperAdmin()
            ? array_keys(Admin::getRoles())
            : [Admin::ROLE_ADMIN];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($admin->id)],
            'login_name' => ['required', 'string', 'max:255', Rule::unique('admins', 'login_name')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'status' => 'required|boolean',
        ], [
            'name.required' => '名前は必須です。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'login_name.required' => 'ログイン名は必須です。',
            'login_name.unique' => 'このログイン名は既に使用されています。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードの確認が一致しません。',
            'role.required' => 'ロールは必須です。',
            'role.in' => '無効なロールが選択されています。',
            'status.required' => 'ステータスは必須です。',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'login_name' => $request->login_name,
            'role' => $request->role,
            'status' => $request->status,
        ];

        // パスワードが入力されている場合のみ更新
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('admin.admins.index')
            ->with('success', '管理者情報が正常に更新されました。');
    }

    /**
     * 管理者を削除
     */
    public function destroy(Admin $admin)
    {
        if (!$this->isSuperAdmin() && $admin->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'スーパー管理者のアカウントは削除できません。');
        }

        // 自分自身は削除できない
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->back()
                ->with('error', '自分自身のアカウントは削除できません。');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', '管理者が正常に削除されました。');
    }
}
