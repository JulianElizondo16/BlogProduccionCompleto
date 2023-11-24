<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.posts.index')->only('index');
        $this->middleware('can:admin.posts.create')->only('create', 'store');
        $this->middleware('can:admin.posts.edit')->only('edit', 'update');
        $this->middleware('can:admin.posts.destroy')->only('destroy');
    }

    public function index()
    {
        return view('admin.posts.index');
    }


    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $tags = Tag::all();


        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /*     ACA TENEMOS QUE PONER NUESTRO POSTREQUEST QUE CREAMOS PARA QUE LO PUEDA VALIDAR*/
    public function store(PostRequest $request)
    {


        /* return Storage::put('public/posts', $request->file('file')); */

        $post = Post::create($request->all());

        if ($request->file('file')) {
            $url = Storage::put('public/posts', $request->file('file'));


            $post->image()->create([
                /* pasamos los campos del registro que queremos agregar */
                'url' => $url
            ]);
        }

        Cache::flush();

        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }
        return redirect()->route('admin.posts.edit', $post);
    }

    public function edit(Post $post)
    {
        /* VAMOS A HACER REFERENCIA AL POLICY QUE CREAMOS */
        $this->authorize('author', $post);
        /* ESTA ES LA SOLUCION DEL ERROR EN LA VISTA EDIT DE POSTS. */
        $categories = Category::pluck('name', 'id');
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }


    public function update(PostRequest $request, Post $post)
    {
        /* VAMOS A HACER REFERENCIA AL POLICY QUE CREAMOS */
        $this->authorize('author', $post);

        $post->update($request->all());

        if ($request->file('file')) {
            $url = Storage::put('public/posts', $request->file('file'));

            if ($post->image) {

                Storage::delete($post->image->url);

                $post->image->update([
                    'url' => $url
                ]);
            } else {
                $post->image()->create([
                    'url' => $url
                ]);
            }
        }
        /*  ESTO ES PARA EDITAR TAMBIEN LAS ETIQUETAS */
        if ($request->tags) {
            $post->tags()->sync($request->tags);
        }

        Cache::flush();

        return redirect()->route('admin.posts.edit', $post)->with('info', 'El post se actualizo con exito');
    }

    public function destroy(Post $post)
    {
        /* VAMOS A HACER REFERENCIA AL POLICY QUE CREAMOS */
        $this->authorize('author', $post);

        /* VAMOS A CREAR UN OBSERVADOR CON php artisan make:observer UserObserver --model=User */
        $post->delete();
        Cache::flush();
        return redirect()->route('admin.posts.index', $post)->with('info', 'El post se elimino con exito');
    }
}
