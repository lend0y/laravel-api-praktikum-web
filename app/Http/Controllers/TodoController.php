<?php
s
namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * GET ALL - menampilkan semua todo (dengan pagination).
     */
    public function index()
    {
        return Todo::paginate(10);
    }

    /**
     * CREATE - Menambahkan todo baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'nullable',
            'priority' => 'nullable|integer',
            'category' => 'nullable'
        ]);

        return Todo::create($data);
    }

    /**
     * GET BY ID - Menampilkan detail todo berdasarkan id.
     */
    public function show($id)
    {
        return Todo::findOrFail($id);
    }

    /**
     * UPDATE - Perbarui data todo berdasarkan id.
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update($request->all());

        return $todo;
    }

    /**
     * DELETE - Soft delete todo berdasarkan id.
     */
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
