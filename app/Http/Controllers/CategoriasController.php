<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Categorias;
use App\Models\Iconos;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CategoriasController extends Controller
{

  /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function index(Request $request)
      {
        $categorias = Categorias::All();
        $iconos = Iconos::All();
        return view('/admin.categorias.index', compact('categorias', 'iconos'));
      }

      public function agregarCategorias(Request $request)
      {
        //Log::info("CategoriasController:agregarCategorias", ["request" => $request->all()]);

        $image = $request->file('file');
        $avatarName = $image->getClientOriginalName();
        $nameonly=preg_replace('/\..+$/', '', $image->getClientOriginalName());
        $image->move(public_path('storage/img/'),$avatarName);
        $avatar = 'storage/img/'.$avatarName;

        $imageUpload = new Iconos();
        $imageUpload->name = $nameonly;
        $imageUpload->path = $avatar;
        $imageUpload->created_by = Auth::user()->id;
        $imageUpload->save();

        $icon = Iconos::All();

        if($request->categoria_subcategoria == "nulo") {
          $categoria = new Categorias();
          $categoria->name = $request->nombre_categoria;
          $categoria->name_co = $request->nombre_co_categoria;
          $categoria->icono_id = ($icon->last()->id);
          $categoria->orden = 1;
          $categoria->activo = 1;
          $categoria->created_by = Auth::user()->id;
          $categoria->save();
        }
        else
        {
          $categoria = new Categorias();
          $categoria->name = $request->nombre_categoria;
          $categoria->name_co = $request->nombre_co_categoria;
          $categoria->icono_id = ($icon->last()->id);
          $categoria->padre = $request->categoria_subcategoria;
          $categoria->orden = 1;
          $categoria->activo = 1;
          $categoria->created_by = Auth::user()->id;
          $categoria->save();
        }

        return view('../admin.categorias.index');
      }

      /**
       * Show the form for creating a new resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function create(Request $request)
      {
          //
      }

      /**
       * Store a newly created resource in storage.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return \Illuminate\Http\Response
       */
      public function store(Request $request, Redirector $redirector)
      {
          //
      }

      /**
       * Display the specified resource.
       *
       * @param  \App\Consultation  $consultation
       * @return \Illuminate\Http\Response
       */
      public function show()
      {
          //
      }

      /**
       * Show the form for editing the specified resource.
       *
       * @param  \App\Consultation  $consultation
       * @return \Illuminate\Http\Response
       */
      public function edit()
      {
          //
      }

}
