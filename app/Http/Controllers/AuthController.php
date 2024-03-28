<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->get('email');
            if (User::where('email', $email)->count() > 0) {
                $msg = 'Já existe uma conta com este e-mail cadastrado no sistema. Faça login.';
                if (!$request->has('email')) $msg = 'Por favor, informe um e-mail.';
                DB::rollback();
                return response()->json(['error' => 'login_error', 'message' => $msg], 401);
            }
            $v = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password'  => 'required|min:3|confirmed',
            ]);
            if ($v->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()
                ], 422);
            }

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            DB::commit();
            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            DB::rollback();
            Log::debug("Erro em register", [
                'dados' => $request->all(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'register_error', 'message' => 'Ocorreu um erro ao fazer cadastro, verifique os dados e tente novamente.'], 401);
        }
    }

    public function login(Request $request)
    {
        try {
                $credentials = $request->only('email', 'password');

                $checkUserExists = User::where('email', $request->get('email'))->first();
                if (!$checkUserExists) {
                    return response()->json(['status' => 'error', 'message' => 'E-mail não encontrado'], 422);
                }

                if (Auth::attempt($credentials)) {
                    $this->guard()->attempt($credentials);
                    $token = Auth::guard('api')->attempt($credentials);
                    return response()->json(['status' => 'success', 'check' => Auth::check(), 'token' => $token], 200)->header('Authorization', $token);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Credenciais inválidas'], 401);
                }
        } catch (Exception $e) {
            Log::debug("Erro login", [
                'dados' => $request->all(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage(), 'message' => 'Dados incorretos ou usuário inexistente. Tente novamente mais tarde.'], 401);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            Log::debug("Erro Auth user", [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage(), 'message' => 'Tente realizar o login novamente.'], 401);
        }
    }
    private function guard()
    {
        return Auth::guard();
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