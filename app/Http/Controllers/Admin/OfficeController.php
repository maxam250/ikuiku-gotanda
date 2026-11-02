<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::orderBy('sort')->orderBy('name')->get();
        return view('admin.offices.index', compact('offices'));
    }

    public function create()
    {
        return view('admin.offices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'memo'      => 'nullable|string',
            'sort'      => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Office::create([
            'name'      => $request->name,
            'memo'      => $request->memo,
            'sort'      => $request->sort ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.offices.index')->with('success', '事務所を作成しました');
    }

    public function edit(Office $office)
    {
        return view('admin.offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'memo'      => 'nullable|string',
            'sort'      => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $office->update([
            'name'      => $request->name,
            'memo'      => $request->memo,
            'sort'      => $request->sort ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.offices.index')->with('success', '事務所を更新しました');
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return redirect()->route('admin.offices.index')->with('success', '事務所を削除しました');
    }
}
