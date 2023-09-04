<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
     public function index()
     {
        //get posts
        $posts = Post::latest()->paginate(5);

        return new PostResource(true, 'List Data Posts', $posts);
     }

     /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg,heic|max:5120',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
         return response()->json($validator->errors(), 422);
        }

        //image
        $image = $request->file('image');
        $image->storeAs('public/post/image', $image->hashName());

        //post
        $post = Post::create([
         'image' =>  $image->hashName(),   
         'title' =>  $request->title,   
         'content' =>  $request->content,   
        ]);

        return new PostResource(true, 'AWESOME! Data Post Berhasil Ditambahkan! :D', $post);
    }

    /**
     * show
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Post $post)
    {
      return new PostResource(true, 'YEAYY! Data Post Ditemukan!', $post);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Post $post)
    {
      $validator = Validator::make($request->all(), [
         'title' => 'required',
         'content' => 'required',
      ]);

      //cek validasi
      if ($validator->fails()) {
         return response()->json($validator->errors(), 422);
      }

      //cek image 
      if ($request->hasFile('image')) {
         
         //upload
         $image = $request->file('image');
         $image->storeAs('public/posts/image', $image->hashName());

         //delete
         Storage::delete('public/posts/image/'.$post->$image);

         //update new image
         $post->update([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
         ]);
      } else {
         //update tanpa gambar
         $post->update([
            'title' => $request->title,
            'content' => $request->content,
         ]);
      }

      return new PostResource(true, 'WOW! Data Post Berhasil Diperbarui!', $post);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Post $post)
    {
      Storage::delete('public/posts/image/'.$post->image);
      $post->delete();
      return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
