<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $todos = Todo::paginate(5);
        return response()->json($todos);
    }

    public function show($id)
    {
        $todo = Todo::find($id);

        if(!$todo) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($todo);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $todo = Todo::create($request->all());
        return response()->json(['message' => 'Todo created'], 201);
    }

    public function update($id, Request $request)
    {
        $todo = Todo::find($id);

        if(!$todo) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $todo->update($request->all());
        return response()->json(['message' => 'Todo updated'], 200);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);

        if(!$todo) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $todo->delete();
        return response()->json(['message' => 'Todo deleted'], 204);
    }

    public function changeStatus($id, $status)
    {
        $todo = Todo::find($id);

        if(!$todo) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if($status === 'done') $todo->done();
        else if($status === 'undone') $todo->undone();
        return response()->json(['message' => 'Todo updated'], 200);
    }
}
