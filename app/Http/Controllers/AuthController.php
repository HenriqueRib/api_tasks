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
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->get('email');
            if (User::where('email', $email)->count() > 0) {
                $msg = 'Já existe uma conta com este e-mail cadastrado no sistema. E não é possivel criar outro.';
                return response()->json(['error' => 'login error', 'message' => $msg], 401);
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
            $params = $request->all();
            $params['password'] = bcrypt($request->password);
            $user = User::create($params);
            DB::commit();
            $credentials = $request->only('email', 'password');
            $token = Auth::guard('api')->attempt($credentials);
            return response()->json(['success' => true, 'token' => $token], 200);
        } catch (Exception $e) {
            DB::rollback();
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
              ];
              Log::channel("auth")->error("Erro register", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'Ocorreu um erro ao fazer cadastro, verifique os dados e tente novamente.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
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
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
              ];
              Log::channel("auth")->error("Erro Login", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'Dados incorretos ou usuário inexistente. Tente novamente mais tarde.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
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
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
              ];
              Log::channel("auth")->error("Erro Auth user", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'Tente realizar o login novamente.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
    }

    private function guard()
    {
        return Auth::guard();
    }
}