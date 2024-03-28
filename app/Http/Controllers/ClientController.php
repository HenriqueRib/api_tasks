<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function create(Request $request){

    }

    public function teste(Request $request)
    {
        $params = $request->all();
        DB::beginTransaction();
        try {
          //code...
          DB::commit();
          $responseData = [
              'status' => true,
              'mensage' => 'Teste Funcionando.',
              'uahsua' => $tet,
          ];
          DB::rollback();
          return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
          //throw;
          DB::rollback();
          Log::channel("tasks")->error("Erro > register", [
              'arquivo' => $e->getFile(),
              'linha' => $e->getLine(),
              'erro' => $e->getMessage(),
          ]);
          $responseData = [
            'status' => true,
            'mensage' => 'Já existe um usuário com este e-mail já cadastrado.',
          ];
          dd($responseData);
          return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}