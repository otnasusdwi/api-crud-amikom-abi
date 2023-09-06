<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
   public function index()
   {
      $post = Post::all();
      return PostResource::collection($post);
   }

   public function show($id)
   {
      $post = Post::with('kategori:id,nama_kategori')->findOrFail($id);
      return new PostDetailResource($post);
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,heic|max:5120',
         'title' => 'required|max:255',
         'content' => 'required',
         'id_kategori' => 'required|exists:kategoris,id'
      ]);

      $image = $request->file('image');
      $imagePath = $image->storeAs('public/post/image', $image->hashName());

      $post = Post::create([
         'image' => $imagePath,
         'title' => $request->input('title'),
         'content' => $request->input('content'),
         'id_kategori' => $request->input('id_kategori'),
      ]);

      if ($post) {
         return (new PostDetailResource($post))->additional(['message' => 'Postingan Berhasil Dibuat!!!']);
     } else {
         return response()->json(['message' => 'Terjadi Kesalahan Saat Membuat Postingan'], 500);
     }
      // return new PostDetailResource($post->loadMissing('kategori'));
   }

   public function update(Request $request, $id)
   {
      $validated = $request->validate([
         'title' => 'required|max:255',
         'content' => 'required',
      ]);

      $post = Post::findOrFail($id);

      if ($request->hasFile('image')) {
         if ($post->image) {
            Storage::delete($post->image);
         }
         $imagePath = $request->file('image')->store('public/posts/image');
         $post->image = $imagePath;
      }

      $post->title = $request->input('title');
      $post->content = $request->input('content');
      $post->id_kategori = $request->input('id_kategori');
      $post->save();

      if ($post) {
         return (new PostDetailResource($post))->additional(['message' => 'Postingan Berhasil Diupdate!!!']);
     } else {
         return response()->json(['message' => 'Terjadi Kesalahan Saat Mengupdate Postingan'], 500);
     }

      // return new PostDetailResource($post->loadMissing('kategori'));
   }

   public function destroy($id)
   {
      $post = Post::findOrFail($id);
      $post->delete();

      if ($post) {
         return (new PostDetailResource($post))->additional(['message' => 'Postingan Berhasil Dihapus!!!']);
     } else {
         return response()->json(['message' => 'Terjadi Kesalahan Saat Menghapus Postingan'], 500);
     }

      // return new PostResource($post);
   }
}
