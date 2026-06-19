<?php

namespace App\Http\Controllers;

use App\Models\EquipmentArticle;
use Illuminate\Http\Request;

class EquipmentArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'Computer');
        $articles = EquipmentArticle::where('equipment_type', $type)
            ->orderBy('name')->get();

        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|in:Computer,Kitchen,Office,Lab,General',
            'name'           => 'required|string|max:150',
        ]);

        $article = EquipmentArticle::create([
            'equipment_type' => $request->equipment_type,
            'name'           => $request->name,
            'is_active'      => true,
        ]);

        return response()->json(['message' => 'Article added.', 'article' => $article]);
    }

    public function update(Request $request, EquipmentArticle $article)
    {
        $request->validate(['name' => 'required|string|max:150']);
        $article->update(['name' => $request->name]);

        return response()->json(['message' => 'Article updated.', 'article' => $article]);
    }

    public function destroy(EquipmentArticle $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article removed.']);
    }
}