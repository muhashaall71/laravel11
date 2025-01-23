<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        try {
            $datas = Post::all();
            return response()->json([
                "status" => "success",
                "data" => $datas,
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                "status" => "error",
                "message" => $err->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function indexReq(Request $request)
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
        try {
            // Ambil data dari request
            $data = $request->all();

            // Aturan validasi
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:post_data,email',
                'profession' => 'required|in:Developer,Teacher,Doctor',
            ];

            // Pesan kesalahan validasi
            $messages = [
                'name.required' => 'Nama harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'profession.required' => 'Profesi harus diisi',
            ];

            // Validasi data
            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            // Buat data baru di tabel post_data
            $post = Post::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'profession' => $data['profession'],
            ]);

            // Kirim response sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dibuat',
                'data' => $post,
            ], 201);
        } catch (\Exception $err) {
            // Tangani error dan kirim response
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage(),
            ], 500);
        }
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
    public function update(Request $request, $id)
    {
        try {
            // Validasi data yang diterima dari request
            $data = $request->all();
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:post_data,email,' . $id,
                'profession' => 'required|in:Developer,Teacher,Doctor',
            ];
    
            $messages = [
                'name.required' => 'Nama harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'profession.required' => 'Profesi harus diisi',
            ];
    
            $validator = Validator::make($data, $rules, $messages);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }
    
            // Cari data berdasarkan ID
            $post = Post::findOrFail($id);
    
            // Update data
            $post->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'profession' => $data['profession'],
            ]);
    
            // Kirim response sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui',
                'data' => $post,
            ], 200);
        } catch (\Exception $err) {
            // Tangani error dan kirim response
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage(),
            ], 500);
        }
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari data berdasarkan ID
            $post = Post::findOrFail($id);
    
            // Hapus data
            $post->delete();
    
            // Kirim response sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
            ], 200);
        } catch (\Exception $err) {
            // Tangani error dan kirim response
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage(),
            ], 500);
        }
    }
    
}
