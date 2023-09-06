<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return KategoriResource::collection($kategori);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $kategori = Kategori::create([
            'nama_kategori' => $request->input('nama_kategori'),
        ]);
    
        if ($kategori) {
            return (new KategoriResource($kategori))->additional(['message' => 'Kategori Berhasil Dibuat!!!']);
        } else {
            return response()->json(['message' => 'Terjadi Kesalahan Saat Membuat Postingan'], 500);
        }

        // return new KategoriResource($kategori);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $kategori = Kategori::findOrFail($id);
        $kategori->nama_kategori = $request->input('nama_kategori');
        $kategori->save();
    
        if ($kategori) {
            return (new KategoriResource($kategori))->additional(['message' => 'Kategori Berhasil Diupdate!!!']);
        } else {
            return response()->json(['message' => 'Terjadi Kesalahan Saat Mengupdate Postingan'], 500);
        }

        // return new KategoriResource($kategori);
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        
        if ($kategori) {
            return (new KategoriResource($kategori))->additional(['message' => 'Kategori Berhasil Dihapus!!!']);
        } else {
            return response()->json(['message' => 'Terjadi Kesalahan Saat Menghapus Postingan'], 500);
        }

        // return new KategoriResource($kategori);
    }
}
