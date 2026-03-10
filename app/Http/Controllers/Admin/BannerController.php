<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\photos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title1' => 'required|string|max:255',
            'title2' => 'nullable|string|max:255',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'button_link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        $banner = Banner::create($data);

        $banner->attachfiles([$request->file('image')] ?? null);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Bannière créée !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {

        $request->validate([
            'title1' => 'required|string|max:255',
            'title2' => 'nullable|string|max:255',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'button_link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();


        $banner->update($data);

        $banner->attachfiles([$request->file('image')] ?? null);  // ✅ ARRAY avec 1 fichier

        return redirect()->route('admin.banners.index')
            ->with('success', 'Bannière modifiée !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

         return redirect()->route('admin.banners.index')
        ->with('success', 'Bannière supprimée avec ses images !');
    }


}
