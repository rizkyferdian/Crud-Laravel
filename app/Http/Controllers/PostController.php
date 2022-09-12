<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }
    public function store(Request $request)
    {
        //Validasi Form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //Upload Image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //Create Post
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil ditambahkan!']);
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        if ($request->hasFile('image')) {

            //Upload Image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts/' . $post->image);

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil diubah!']);
    }

    public function destroy(Post $post)
    {
        //delete Image
        Storage::delete('public/posts/' . $post->image);
        //delete post
        $post->delete();
        //redirect
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil dihapus!']);
    }
}
