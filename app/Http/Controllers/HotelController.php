<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCate;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $categories = EquipmentCate::where('active', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $query = Equipment::with([
            'equipment_cate',
            'equipmentSubImages',
            'equipmentListImages',
            'accessoryListImages',
        ])
            ->where('active', 1)
            ->orderBy('sort')
            ->orderBy('id');

        if ($categoryId) {
            $query->where('cate_id', $categoryId);
        }

        $equipments = $query->paginate(25)->withQueryString();

        $breadcrumbs = [
            ['label' => 'HOME', 'url' => route('top')],
            ['label' => 'ホテル・スタジオ', 'url' => route('hotels')],
        ];

        return view('hotel', compact('equipments', 'categories', 'categoryId', 'breadcrumbs'));
    }
}
