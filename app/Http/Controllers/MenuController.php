<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();
        $permissions = Permission::orderBy('name')->get();
        return view('menus.create', compact('parents', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'slug'  => 'required|string|unique:menus,slug',
            'url'   => 'nullable|string|max:255',
            'icon'  => 'nullable|string|max:100',
            'order' => 'integer',
        ]);

        Menu::create($request->only('name', 'slug', 'url', 'icon', 'parent_id', 'order', 'is_active', 'permission_name'));
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dibuat.');
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->orderBy('order')->get();
        $permissions = Permission::orderBy('name')->get();
        return view('menus.edit', compact('menu', 'parents', 'permissions'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'slug'  => 'required|string|unique:menus,slug,' . $menu->id,
            'url'   => 'nullable|string|max:255',
            'icon'  => 'nullable|string|max:100',
            'order' => 'integer',
        ]);

        $menu->update($request->only('name', 'slug', 'url', 'icon', 'parent_id', 'order', 'is_active', 'permission_name'));
        return redirect()->route('menus.index')->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}
