<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColorController extends Controller
{
    public function index()
    {
        $name = Auth::user()->name;
        $colors = Color::all();
        return view('admin.colors.list', compact('name', 'colors'));
    }
    public function create()
    {
        $products = Product::orderBy('id', 'DESC')->get();
        return view('admin.colors.create', compact('products'));
    }
    public function store(ColorRequest $request)
    {
        $color = new Color();
        $color->color = $request->input('color');
        $color->product_id = $request->input('product_id');
        $color->quantity = $request->input('quantity');
        $color->save();
        return redirect()->route('colors.index');
    }
    public function show($id)
    {
        $color = Color::findOrFail($id);
        return view('admin.colors.show', compact('color'));
    }
    public function edit($id)
    {
        $color = Color::findOrFail($id);
        $products = Product::orderBy('id', 'DESC')->get();
        return view('admin.colors.edit', compact('color', 'products'));
    }
    public function update(ColorRequest $request, $id)
    {
        $color = Color::findOrFail($id);
        $color->color = $request->input('color');
        $color->product_id = $request->input('product_id');
        $color->quantity = $request->input('quantity');
        $color->save();
        return redirect()->route('colors.index');
    }
    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        $color->delete();
        return redirect()->route('colors.index')->with('success', 'Color deleted successfully!');
    }    
}
