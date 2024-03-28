<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class TaskController extends Controller
{
  
    public function listAll(Request $request){
      try {
        $tasks = Task::all();
        return response()->json(['success' => true, 'tasks' => $tasks], 200); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("tasks")->error("Erro > listAll", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'Ocorreu um erro ao realizar o consulta de todas as tarefas.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function create(Request $request)
    {
      try {
        $params = $request->all();
        DB::beginTransaction();
        $task = Task::create($params);
        DB::commit();
        $responseData = [
          'status' => true,
          'mensage' => 'Sua tarefa foi cadastrada com sucesso!.',
          'dados' => $params,
          'task' => $task
        ];
        return response()->json(['success' => true, 'responseData' => $responseData], 200);
      } catch (\Exception $e) {
        DB::rollback();
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("tasks")->error("Erro > create", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'Ocorreu um erro ao realizar o cadastro de uma tarefa.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function show(Request $request, $id){
      try {
        $task = Task::find($id);
        if(isset($task)){
          return response()->json(['success' => true, 'task' => $task], 200); 
        }
        return response()->json(['success' => false, 'task' => $task, 'message' => "Nenhuma tarefa foi encontrada com o id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("tasks")->error("Erro > show", $errorData);
        $responseData = [
            'status' => false,
            'message' => "Ocorreu um erro ao realizar a consulta da tarefa com id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function update(Request $request, $id){
      try {
        $task = Task::find($id);
        if(isset($task)){
          $params = $request->all();
          $task->update($params);
          return response()->json(['success' => true, 'task' => $task], 200); 
        }
        return response()->json(['success' => false, 'task' => $task, 'message' => "Nenhuma tarefa foi encontrada com o id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("tasks")->error("Erro > update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "Ocorreu um erro ao realizar a atualização da tarefa com id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
    
    public function delete(Request $request){
      try {
        $params = $request->all();
        $task = Task::find($params['id']);
        if(isset($task)){
          $task->delete();
          return response()->json(['success' => true, 'message' => "A tarefa $task->name foi deletada com sucesso!"], 200); 
        }
        return response()->json(['success' => false, 'task' => $task, 'message' => "Nenhuma tarefa foi encontrada com o id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("tasks")->error("Erro > update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "Ocorreu um erro ao deletar tarefa",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  }
