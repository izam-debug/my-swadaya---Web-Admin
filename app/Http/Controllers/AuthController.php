<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $username = 'username';
    public function username()
    {
        return 'username';
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        // Validasi tambahan untuk level dan status pengguna
        $user = User::where('username', $credentials['username'])
                ->where(function ($query) {
                    $query->where('level', 'admin')
                        ->orWhere('level', 'super admin');
                })
                ->where('status', 'aktif')
                ->first();

        if (!$user || !Auth::attempt($credentials)) {
            return back()->with('loginError', 'Login Gagal');
        }

        $request->session()->regenerate();

        return redirect()->intended('dashboard');
    }


    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }

    // public function apiLogin(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'username' => ['required'],
    //         'password' => ['required']
    //     ]);
    
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    
    //         // Membuat payload token
    //         $payload = [
    //             'user_id' => $user->id,
    //             'username' => $user->username,
    //             // tambahkan informasi lainnya yang ingin Anda sertakan dalam token
    //         ];
    
    //         // Kunci rahasia untuk menandatangani token
    //         $key = 'your_secret_key';
    
    //         // Membuat token dengan JWT
    //         $token = JWT::encode($payload, $key, 'HS256');
    
    //         // Menyiapkan data untuk respons JSON
    //         $responseData = [
    //             'data' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'name' => $user->username,
    //                 'token' => $token
    //             ],
    //             'message' => "User Login",
    //             'status' => 200
    //         ];
    
    //         // Mengirimkan respons JSON
    //         return response()->json($responseData);
    //     }
    
    //     return response()->json(['message' => 'Login Gagal'], 401);
    // }
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        // Validasi tambahan untuk level dan status pengguna
        $user = User::where('username', $credentials['username'])
                    ->where(function ($query) {
                        $query->where('level', 'petugas')
                            ->where('status', 'aktif');
                    })
                    ->first();

        if (!$user || !Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login Gagal'], 401);
        }

        $user = Auth::user();

        $responseData = [
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'token' => $user->token
            ],
            'message' => 'User Login',
            'status' => 200
        ];

        // Mengirimkan respons JSON
        return response()->json($responseData);
    }


    public function list_petugas()
    {
        $petugas = User::where('level', '=', 'petugas')->get();
        // $petugas = User::all();
        return view('layout.master-petugas', compact('petugas'));
    }
    public function generateRandomString($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public function add_petugas(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama' => 'required|string|max:255',
        'username' => 'required|string|alpha_dash|max:10|unique:client',
        'password' => '',
        'status' => '',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Generate random token
    $token = $this->generateRandomString(64);

    $password = $request->input('password') ? bcrypt($request->input('password')) : bcrypt('petugas123');
    $petugas = new User([
        'name' => $request->input('nama'),
        'username' => $request->input('username'),
        'password' => $password,
        'status' => $request->input('status'),
        'level' => 'petugas',
        'token' => $token, // Simpan token
    ]);
    // return($petugas);
    
    $petugas->save();
    return redirect()->route('master-petugas')->with('success', 'Data berhasil disimpan.');
}

    
    public function edit_petugas($id)
    {
        $petugas = User::findorfail($id);
        return response()->json($petugas);
    }

    public function update_petugas(Request $request)
    {
        $id = $request->input('id_petugas');
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:10|unique:users,username,' . $id,
            'password' => '',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Simpan data ke dalam tabel clients
        
        $petugas = User::find($id);
        $petugas->name = $request->input('nama');
        $petugas->username = $request->input('username');
        if ($request->filled('password')) {
            $petugas->password = bcrypt($request->input('password'));
        }
        $petugas->status = $request->input('status');
        $petugas->update();
        return redirect()->route('master-petugas')->with('success', 'Data berhasil diedit.');
    }

    public function delete_petugas($id)
    {
        $client = User::findOrFail($id);
        $client->delete();
    
        return redirect()->route('master-petugas')->with('success', 'Data berhasil dihapus.');
    }

    public function dashboard()
    {
        $timestamps = Tagihan::select(DB::raw('YEAR(created_at) as year'))
        ->groupBy(DB::raw('YEAR(created_at)'))
        ->get()
        ->pluck('year')
        ->all();
        // return($timestamps);
        $selectedYear = date('Y');
        return view('layout.dashboard', compact('timestamps', 'selectedYear'));
    }

}
