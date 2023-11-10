<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\PostController;
use App\Models\Comentario;
use App\Models\Post;


//Aca lo que estamos haciendo es la llamada a las rutas

//el primer 'asd' es al URL aca lo que estamos haciedno es que con el ->name('') le estamos dando un nombre a esa ruta para llamarla
Route::get('/', HomeController::class)->name('home');



//ACA ESTAMOS LLAMANDO A TODOS LOS CONTROLADORES DE COMENTARIOS CONTROLLER

Route::controller(ComentariosController::class)->group(function(){

Route::get('comentarios', 'index')->name('comentarios.home');

Route::post('comentarios', 'GenerarComentario')->name('comentarios.generar');

//muestra el Comentario a detalle
Route::get('comentarios/{comentario}', 'show')->name('comentarios.show');

//EDITAR EL Comentario
Route::get('comentarios/{comentario}/edit', 'edit') ->name('comentarios.edit');

//PARA GUARDAR LOS DATOS
Route::put('comentarios/{comentario}', 'update')->name('comentarios.update');

//Eliminar DATOS DE LA TABLA
Route::delete('comentarios/{comentario}','destroy')->name('comentarios.delete');
});

//ACA VAMOS A LLAMAR A LOS POST QUE ESTEMOS HACIENDO NUEVOS 30-10-23
Route::get('posts', [PostController::class, 'index'])->name('post.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('category/{category}', [PostController::class, 'category'])->name('posts.category');
Route::get('tag/{tag}',[PostController::class, 'tag'])->name('posts.tag');













Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
