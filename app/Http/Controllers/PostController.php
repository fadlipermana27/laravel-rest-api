<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Auth;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        // return response()->json(['data' => $posts]);
        return PostDetailResource::collection($posts->loadmissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function show($id)
    {
        
        $post = Post::with('writer:id,username')->findOrFail($id);
        return new PostDetailResource($post->loadmissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content']));
            // $post = Post::with('writer:id,username')->find($id);

            // if($post == null){
            //     return response()->json(['message' => "data not found",
            //     'data' => []]);
            // }
        
        // dd($post);
        // return response()->json(['data' => $post]);
    }

    public function show2($id)
    {
        $post = Post::findOrFail($id);
        return new PostDetailResource($post);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        $post = Post::findOrFail($id);
        // if($post){
        //     return response()->json(['message' => "data not found",
        //     'data' => []]);
        // }
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function show3()
    {
        $author = Auth::user()->id;
        $post = Post::with('writer:id,username')->where('author', $author)->get();

        if($post == null){
            return response()->json(['message' => "data not found"]);
        }
        return response()->json(['data' => $post]);

    }
}
