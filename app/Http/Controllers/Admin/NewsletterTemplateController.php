<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterTemplate;
use Illuminate\Http\Request;

class NewsletterTemplateController extends Controller
{
    /**
     * テンプレート一覧
     */
    public function index()
    {
        $templates = NewsletterTemplate::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.newsletter.templates.index', compact('templates'));
    }

    /**
     * テンプレート作成フォーム
     */
    public function create()
    {
        return view('admin.newsletter.templates.create');
    }

    /**
     * テンプレート保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_text' => 'nullable|string',
            'body_html' => 'nullable|string',
        ], [
            'name.required' => 'テンプレート名を入力してください。',
            'subject.required' => '件名を入力してください。',
        ]);

        NewsletterTemplate::create($validated);

        return redirect()->route('admin.newsletter.templates.index')
            ->with('success', 'テンプレートを作成しました。');
    }

    /**
     * テンプレート編集フォーム
     */
    public function edit($id)
    {
        $template = NewsletterTemplate::findOrFail($id);
        return view('admin.newsletter.templates.edit', compact('template'));
    }

    /**
     * テンプレート更新
     */
    public function update(Request $request, $id)
    {
        $template = NewsletterTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_text' => 'nullable|string',
            'body_html' => 'nullable|string',
        ], [
            'name.required' => 'テンプレート名を入力してください。',
            'subject.required' => '件名を入力してください。',
        ]);

        $template->update($validated);

        return redirect()->route('admin.newsletter.templates.index')
            ->with('success', 'テンプレートを更新しました。');
    }

    /**
     * テンプレート削除
     */
    public function destroy($id)
    {
        $template = NewsletterTemplate::findOrFail($id);
        
        // 使用中のキャンペーンがあるかチェック
        if ($template->campaigns()->count() > 0) {
            return redirect()->route('admin.newsletter.templates.index')
                ->with('error', 'このテンプレートは使用中のため削除できません。');
        }

        $template->delete();

        return redirect()->route('admin.newsletter.templates.index')
            ->with('success', 'テンプレートを削除しました。');
    }

    /**
     * テンプレートデータ取得（API）
     */
    public function getData($id)
    {
        $template = NewsletterTemplate::findOrFail($id);
        
        return response()->json([
            'subject' => $template->subject,
            'body_text' => $template->body_text,
            'body_html' => $template->body_html,
        ]);
    }

    /**
     * テンプレートプレビュー
     */
    public function preview($id)
    {
        $template = NewsletterTemplate::findOrFail($id);
        return view('admin.newsletter.templates.preview', compact('template'));
    }

    /**
     * テンプレート複製
     */
    public function duplicate($id)
    {
        $template = NewsletterTemplate::findOrFail($id);
        
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (コピー)';
        $newTemplate->save();

        return redirect()->route('admin.newsletter.templates.edit', $newTemplate->id)
            ->with('success', 'テンプレートを複製しました。');
    }
}
