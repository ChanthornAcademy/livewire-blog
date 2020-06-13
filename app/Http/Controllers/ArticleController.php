<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::with('user')->orderBy('id','desc')->paginate(10);
        return view('articles.index',compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'=>'required|unique:articles,title',
            'body'=>'required|min:5',
            'image'=>'image|required',
            'active'=>'integer'
        ]);

        if($request->hasFile('image')){
            $path = $request->file('image');
            $fileExtension = $request->file('image')->extension();
            $resized = Image::make($path)->resize('900',null,function($constraint){
                $constraint->aspectRatio();
            })->encode($fileExtension);
            $fileName = time().Str::slug($validatedData['title']).'.'.$fileExtension;
            Storage::disk('article')->put($fileName,$resized);
            $validatedData['image'] = $fileName;
            // dd($validatedData);
        }



        auth()->user()->articles()->create($validatedData);
        return redirect()->route('article.index')->with('message','Create Successfully!!!');
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
        $article = auth()->user()->articles()->findOrFail($id);
        return view('articles.edit',compact('article'));
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
        $article = auth()->user()->articles()->findOrFail($id);
        $validatedData = $request->validate([
            'title'=>'required|unique:articles,title,'.$id,
            'body'=>'required|min:5',
            'image'=>'image',
            'active'=>'integer'
        ]);


        if($request->hasFile('image')){
            if(Storage::disk('article')->exists($article->image)){
                Storage::disk('article')->delete($article->image);
            }
            $path = $request->file('image');
            $fileExtension = $request->file('image')->extension();
            $resized = Image::make($path)->resize('900',null,function($constraint){
                $constraint->aspectRatio();
            })->encode($fileExtension);
            $fileName = time().Str::slug($validatedData['title']).'.'.$fileExtension;
            Storage::disk('article')->put($fileName,$resized);
            $validatedData['image'] = $fileName;
            // dd($validatedData);
        }


        $article->update($validatedData);
        return redirect()->route('article.index')->with('message','Update Successfully!!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = auth()->user()->articles()->findOrFail($id)->delete();
        if(Storage::disk('article')->exists($article->image)){
            Storage::disk('article')->delete($article->image);
        }
        return redirect()->back()->with('message','Delet Successfully!!!');
    }
}
