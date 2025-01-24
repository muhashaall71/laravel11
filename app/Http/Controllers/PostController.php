<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

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

    public function indexFilter(Request $request)
    {
        try {
            // Ambil query parameter untuk pencarian nama dan email
            $search = $request->query('search');
            $professionFilter = $request->query('profession');
            $perPage = $request->query('per_page', 10); // Default 10 item per halaman
    
            // Query dasar untuk mengambil data
            $query = Post::query();
    
            // Jika ada parameter pencarian (search) di query string
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
    
            // Jika ada parameter profesi (profession) di query string
            if ($professionFilter) {
                $query->where('profession', $professionFilter);
            }
    
            // Urutkan berdasarkan waktu update terakhir
            $query->orderBy('updated_at', 'desc');
    
            // Lakukan pagination
            $posts = $query->paginate($perPage);
    
            // Dapatkan daftar profesi unik untuk filter
            $professions = Post::select('profession')->distinct()->pluck('profession');
    
            // Kembalikan data dalam format JSON
            return response()->json([
                'status' => 'success',
                'data' => $posts->items(), // Data pada halaman saat ini
                'pagination' => [
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                ],
                'professions' => $professions, // Daftar profesi untuk filter
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage(),
            ], 500);
        }
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
        try {
            // Mencari data berdasarkan ID
            $post = Post::findOrFail($id);
    
            // Mengembalikan data dalam bentuk JSON
            return response()->json([
                'status' => 'success',
                'data' => $post,
            ], 200);
        } catch (\Exception $err) {
            // Menangani error jika data tidak ditemukan
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
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
    
    /**
     * Mengambil data profesi dan jumlahnya.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfessions(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // Jika start_date atau end_date kosong, gunakan nilai default
            if (!$startDate || !$endDate) {
                $startDate = Carbon::yesterday()->toDateString();
                $endDate = Carbon::today()->toDateString(); 
            }

            // Query dengan filter tanggal
            $professions = DB::table('post_data')
                ->select('profession', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate]) 
                ->groupBy('profession')
                ->get();

            if ($professions->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No data found for the selected date range.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $professions
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data profesi.',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function getDateAggregation(Request $request)
    {
        try {
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');

            if (empty($start_date) || empty($end_date)) {
                $start_date = Carbon::yesterday()->toDateString();
                $end_date = Carbon::today()->toDateString();
            }

            $data = DB::table('post_data')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'), 'asc') 
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data agregasi tanggal.',
                'error' => $err->getMessage(),
            ], 500);
        }
    }
}

