<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Validator;

use App\User;
use App\UsuarioListaDesejo;

class UsuariosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuario = User::find(Auth::user()->id);

        $arrRetorno = [
          'usuario' => $usuario
        ];

        return view('usuario.meus-dados', $arrRetorno);
    }

    /**
     * Metódo com regras para inserção ou edição
     */
    protected function regrasSalvar()
    {
      return [
        "nome" => "required|string|max:255",
        "email" => "required|string|max:255"
      ];
    }

    /**
     * Método que edita ou salva uma model
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editar(Request $request)
    {
      $validator = Validator::make(Input::all(), $this->regrasSalvar());

      if ($validator->fails()) {
          return back()->withErrors($validator->messages());
      }

      $usuario = User::findOrFail(Auth::user()->id);
      $usuario->nome = $request->nome;
      $usuario->email = $request->email;

      try {
        $usuario->save();
        return back()->with("success", ["Usuário alterado com sucesso."]);

      } catch (\Illuminate\Database\QueryException $e) {
        return back()->withErrors(["Não foi possível concluir a operação, tente novamente."]);
      }

    }

    /**
     * Show the application
     *
     * @return \Illuminate\Http\Response
     */
    public function lista()
    {
        $usuario = User::findOrFail(Auth::user()->id);

        $arrRetorno = [
          'usuario' => $usuario
        ];

        return view('usuario.lista-desejos', $arrRetorno);
    }


    /**
     * Método que insere itens na lista de desejos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function itemCadastrar(Request $request)
    {
        $lista = UsuarioListaDesejo::findOrNew($request->id);
        $lista->id_usuario = Auth::user()->id;
        $lista->desc_item = $request->desc_item;
        $lista->valor = $request->valor;
        $lista->link = $request->link;
        $lista->observacao = $request->observacao;

        $lista->save();
        try {
          return back()->with("success", ["Item registrado com sucesso."]);

        } catch (\Illuminate\Database\QueryException $e) {
          return back()->withErrors(["Não foi possível concluir a operação, tente novamente."]);
        }
    }

    /**
     * Metódo para logout do usuário, que redericiona para página inicial
     */
    public function logout()
    {
        Auth::logout();
        return redirect("/login");
    }

    public function itemListar(int $idItem)
    {
      $usuario = User::findOrFail(Auth::user()->id);
      $item = UsuarioListaDesejo::where("id", $idItem)->get();
      
      $arrRetorno = [
        'usuario' => $usuario,
        'item' => $item
      ];

      return view('usuario.lista-desejos', $arrRetorno);
    }

}
