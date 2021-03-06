<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Post;
use App\Tag;
use Session;
use Auth;
class PostsController extends Controller
{
    public function __construct()
    {
      $this->middleware('admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(100);
        return view('admin.posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        if($categories->count() == 0 || $tags->count() == 0)
        {
            Session::flash('info', 'You must have some categories and tags befor attempting to careat a post');
            return redirect()->back();
        }
        return view('admin.posts.create')->with('categories', $categories)->with('tags', $tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request,[
            'title' => 'required|max:255',
            'featured' => 'required|image',
            'content' => 'required',
            'category_id' => 'required',
            'tags' => 'required'
        ]);
        $featured = $request->featured;
        $featured_new_name = time().$featured->getClientOriginalName();
        $featured->move('uploads/posts', $featured_new_name);
        //save in database by normally is $post = new Post;$post->save();
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'featured' => 'uploads/posts/'.$featured_new_name,
            'category_id' => $request->category_id,
            'slug' => str_slug($request->title),
            'user_id' => Auth::id()
        ]);
        //Model Post's tags() the same loop for then insert
        $post->tags()->attach($request->tags);
        Session::flash('success', 'Post create succesfully');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        return view('admin.posts.edit')->with('post',$post)
                                       ->with('categories',Category::all())
                                       ->with('tags', Tag::all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required'
        ]);
        $post = Post::find($id);
        if($request->hasFile('featured'))
        {
            $featured = $request->featured;
            $featured_new_name = time().$featured->getClientOriginalName();
            $featured->move('uploads/posts', $featured_new_name);
            $post->featured = 'uploads/posts/'.$featured_new_name;
        }
        $post->title = $request->title;
        $post->content = $request->content;
        $post->category_id = $request->category_id;
        $post->save();
        //Model Post's tags() the same loop for then update
        $post->tags()->sync($request->tags);
        Session::flash('update', 'The post was updated!!');
        return redirect()->route('posts');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Delete status
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        Session::flash('delete', 'The post was trashed!!');
        return redirect()->back();
    }
    // Fetch data trash
    public function trashed()
    {
        $posts = Post::onlyTrashed()->get();
        return view('admin.posts.trashed')->with('posts', $posts);
    }
    // Delete true
    public function kill($id)
    {
        $post = Post::withTrashed()->where('id', $id)->first();
        $post->forceDelete();
        Session::flash('delete', 'Deleted permanently');
        return redirect()->back();

    }

    public function restore($id)
    {
        $post = Post::withTrashed()->where('id', $id)->first();
        $post->restore();
        Session::flash('success', 'Data have been restore');
        return redirect()->route('posts');
    }
}
