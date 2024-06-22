<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $client = Client::all();
        return view('layout.master-pelanggan', compact('client'));
    }

    public function add_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:10|unique:client',
            'password' => '',
            'no_whatsapp' => 'required|numeric|max:9999999999999',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dusun = $request->dusun;
        $kode_terakhir = DB::table('client')
                            ->whereRaw("LEFT(kode_client, 2) = ?", [$dusun])
                            ->latest()
                            ->value('kode_client');
        if ($kode_terakhir == null )
        {
            $kode_sekarang = "001";
            $kode_client = $dusun.$kode_sekarang;
        } else
        {
            
            $kode_client = intval($kode_terakhir) + 1;
        }

        

        // Simpan data ke dalam tabel clients
        // $kode = Client::generateUniqueCode();
        $password = $request->input('password') ? bcrypt($request->input('password')) : bcrypt('pelanggan123');
        $client = new Client([
            'nama_client' => $request->input('nama'),
            'username' => $request->input('username'),
            'password' => $password,
            'no_whatsapp' => $request->input('no_whatsapp'),
            'kode_client' => $kode_client,
        ]);
        $client->save();
        return redirect()->route('master-client')->with('success', 'Data berhasil disimpan.');
    }
    public function edit_client($id)
    {
        $data = Client::findOrFail($id);
        $dusun = substr($data->kode_client, 0, 2);
        return response()->json([$data, $dusun]);
    }
    public function update_client(Request $request)
    {
        $id = $request->input('id_client');
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:10|unique:client,username,' . $id,
            'password' => 'required|string|min:6',
            'no_whatsapp' => 'required|numeric|max:9999999999999',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        
        
       
        $client = Client::find($id);

        $dusun = $request->edit_dusun;
        $kode_terakhir = DB::table('client')
                            ->whereRaw("LEFT(kode_client, 2) = ?", [$dusun])
                            ->latest()
                            ->value('kode_client');
        $kode_pelanggan_sekarang = DB::table('client')
                            ->selectRaw("LEFT(kode_client, 2)")
                            ->where('id', $id)
                            ->pluck(DB::raw("LEFT(kode_client, 2)"))
                            ->first();
        // return([$kode_pelanggan_sekarang, $dusun]);
        // return($request);
        if ($kode_pelanggan_sekarang == $dusun)
        {
            $kode_client = $client->kode_client;
        }
        else
        {
            if ($kode_terakhir == null )
            {
                $kode_sekarang = "001";
                $kode_client = $dusun.$kode_sekarang;
            } else
            {
                
                $kode_client = intval($kode_terakhir) + 1;
            }
        }

        $client->nama_client = $request->input('nama');
        $client->username = $request->input('username');
        if ($request->filled('password')) {
            $client->password = bcrypt($request->input('password'));
        }
        $client->kode_client = $kode_client;
        $client->no_whatsapp = $request->input('no_whatsapp');
        $client->update();
        return redirect()->route('master-client')->with('success', 'Data berhasil diedit.');
    }

    public function delete_client($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
    
        return redirect()->route('master-client')->with('success', 'Data berhasil dihapus.');
    }

    protected $username = 'username';
    public function username()
    {
        return 'username';
    }

    public function loginForm()
    {
       
        if (Auth::guard('client')->check()) {
            return redirect()->route('client-dashboard');
        }

        // Jika belum, tampilkan halaman login
        return view('layout.client-login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('client')->attempt($credentials)) {
            // Jika autentikasi berhasil, redirect ke dashboard client
            return redirect()->route('client-dashboard');
        }

        // Jika autentikasi gagal, kembali ke halaman login dengan pesan error
        return back()->with('loginError', 'Email atau password salah');

    }

    public function dashboard()
    {
        if (Auth::guard('client')->check()) {
            return view('layout.dashboard-client');
        }

        return redirect()->route('login-client')->with('loginError', 'Silakan login terlebih dahulu.');
    }

    
}
