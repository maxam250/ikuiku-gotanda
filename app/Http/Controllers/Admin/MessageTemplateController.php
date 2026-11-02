<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = MessageTemplate::query();

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $templates = $query->latest()->paginate(10);

        return view('admin.message-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.message-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:member,cast,common',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        MessageTemplate::create($validated);

        return redirect()
            ->route('admin.message-templates.index')
            ->with('success', 'テンプレートを作成しました。');
    }

    public function edit(MessageTemplate $template)
    {
        return view('admin.message-templates.edit', compact('template'));
    }

    public function update(Request $request, MessageTemplate $template)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:member,cast,common',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $template->update($validated);

        return redirect()
            ->route('admin.message-templates.index')
            ->with('success', 'テンプレートを更新しました。');
    }

    public function destroy(MessageTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.message-templates.index')
            ->with('success', 'テンプレートを削除しました。');
    }

    public function preview(MessageTemplate $template)
    {
        return response()->json([
            'content' => $template->content
        ]);
    }
} 