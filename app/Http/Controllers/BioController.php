<?php

namespace App\Http\Controllers;

use App\Models\Bio;
use Illuminate\Http\Request;

class BioController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'website' => 'nullable|url',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'skills' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'complete_address' => 'nullable|string',
            'education' => 'nullable|string',
            'work_experience' => 'nullable|string',
        ]);

        // Ubah skills ke array lalu encode ke JSON
        if ($request->filled('skills')) {
            $validated['skills'] = $request->skills;
        }

        // Proses upload avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $validated['user_id'] = auth()->id();

        // Cek jika user sudah memiliki bio, lakukan update, jika belum buat baru
        $bio = Bio::updateOrCreate(
            ['user_id' => auth()->id()], // kondisi pencarian, berdasarkan user_id
            $validated // data yang akan di-update atau disimpan
        );

        return redirect()->back()->with('success', 'Bio berhasil disimpan!');
    }
    public function edit()
    {
        $bio = Bio::where('user_id', auth()->id())->first();

        return view('profile.edit', compact('bio'));
    }
    
    public function storeEducation(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|string',
            'major' => 'required|string',
            'university' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $bio = Bio::firstOrCreate(['user_id' => auth()->id()]);

        $education = $bio->education ?? [];
        $education[] = $data;

        $bio->update(['education' => $education]);

        return redirect()->back()->with('success', 'Education added successfully.');
    }

}
