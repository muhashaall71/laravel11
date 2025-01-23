<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;


class PostController extends Controller
{
    public function indexAll()
    {
        $query = Post::all();
        return response()->json($query, 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Filter by profession
        if ($request->has('profession') && $request->profession) {
            $query->where('profession', $request->profession);
        }

        // Search across all columns
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('profession', 'like', "%{$request->search}%");
            });
        }

        // Order by last updated
        $datas = $query->orderBy('updated_at', 'desc')->paginate(10);

        return response()->json($datas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:datas',
            'profession' => 'required|in:Developer,Teacher,Doctor',
        ]);

        $data = Post::create($validated);

        return response()->json([
            'message' => 'Data created successfully',
            'data' => $data,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Post::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Post::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:datas,email,' . $id,
            'profession' => 'sometimes|in:Developer,Teacher,Doctor',
        ]);

        $data->update($validated);

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Post::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Data Management deleted successfully.']);
    }
}
