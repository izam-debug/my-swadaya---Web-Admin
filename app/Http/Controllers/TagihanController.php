<?php

namespace App\Http\Controllers;

use App\Models\Air;
use App\Models\Client;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class TagihanController extends Controller
{
    public function harga_air()
    {
        // $harga = file_get_contents('harga-air.txt');
        // return view('layout.master-air', compact('harga'));
        $harga = Air::pluck('harga')->first();
        return view('layout.master-air', compact('harga'));
    }

    public function update_harga(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harga_air' => 'required|digits_between:1,5|numeric',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->all());
        }
    
        $harga_baru = $request->input('harga_air');
        $harga = Air::first();
        $harga->harga = $harga_baru;
        $harga->update();
    
        return redirect()->route('master-air')->with('success', 'Harga Air Berhasil Diubah.');
    }
    

    public function insert(Request $request)
    {
        $token = $request->input('token');
    
        // Cek apakah token ada
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
    
        // Cek kebenaran token dengan data user pada database
        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    
        // Lakukan validasi terhadap data yang diterima
        $validator = Validator::make($request->all(), [
            'kode_client' => 'required',
            'nomor_meter' => 'required|numeric',
            'id_petugas' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 400);
        }
    
        // Ambil nomor meter terakhir yang disimpan
        $pelanggan = $request->input('kode_client');

        $kode = Client::where('kode_client', $pelanggan)->first();

        if(!$kode) {
            return response()->json('Pelanggan tidak ditemukan', 400);
        }

        $tagihanTerakhir = Tagihan::where('kode_client', $pelanggan)
            ->whereMonth('created_at', now()->month) // Mencari tagihan untuk bulan ini
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorMeterTerakhir = Tagihan::where('kode_client', $pelanggan)
            ->orderBy('created_at', 'desc')
            ->pluck('nomor_meter')
            ->first();

        if(!$nomorMeterTerakhir) {
            return response()->json('Belum ada tagihan sebelumnya', 400);
        }

        if ($tagihanTerakhir) {
            return response()->json('Tagihan untuk bulan ini sudah ada', 400);
        }
    
        // Ambil nomor meter terakhir yang disimpan
        $nomorMeterSekarang = $request->input('nomor_meter');
    
        // Ambil harga air
        $hargaAir = Air::first();
        $tagihanBaru = new Tagihan();
        $tagihanBaru->kode_client = $pelanggan;
        $tagihanBaru->id_petugas = $request->input('id_petugas');
        $tagihanBaru->nomor_meter = $nomorMeterSekarang;
        $tagihanBaru->pemakaian = $nomorMeterSekarang - $nomorMeterTerakhir;
        $tagihanBaru->tagihan = $tagihanBaru->pemakaian * $hargaAir->harga;
        $tagihanBaru->save();
    
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function get()
    {
        $tagihan = Tagihan::all();
        return response()->json($tagihan, 200, [], JSON_PRETTY_PRINT);
    }

    public function siarkan_tagihan()
    {
        $daftar_tagihan = new Tagihan();
        $tagihan = $daftar_tagihan->index_tagihan()
                                  ->where('status_tagihan', '=', 'Belum Disiarkan');

        return view('layout.siarkan', compact('tagihan'));
    }

    public function index_tagihan()
    {
        $daftar_tagihan = new Tagihan();
        $tagihan = $daftar_tagihan->index_tagihan()
                                  ->where('status_tagihan', '!=', 'Belum Disiarkan');

        return view('layout.tagihan', compact('tagihan'));
    }

    public function bayar_tagihan($id)
    {
        $tagihan = Tagihan::find($id);
        $tagihan->status_tagihan = "Dibayar";
        $tagihan->update();
        
        return redirect()->back();
    }

    public function kwitansi($id)
    {
        setlocale(LC_TIME, 'id_ID'); 
        $tagihan = new Tagihan();
        $kwitansi = $tagihan->kwitansi($id);
    
        $tagihan_sekarang = $kwitansi['tagihan_sekarang'];
        $tagihan_sebelumnya = $kwitansi['tagihan_sebelumnya'];
        $bulan_lalu = $tagihan_sebelumnya ? date('F', strtotime($tagihan_sebelumnya->created_at)) : null;
        $tahun_lalu = $tagihan_sebelumnya ? date('Y', strtotime($tagihan_sebelumnya->created_at)) : null;
        
        $bulan_lalu = $tagihan_sebelumnya ? date('F', strtotime($tagihan_sebelumnya->created_at)) : null;
        $bulan_sekarang = $tagihan_sekarang ? date('F', strtotime($tagihan_sekarang->created_at)) : null;
        function convertToIndonesianMonth($englishMonth) {
            switch ($englishMonth) {
                case 'January':
                    return 'Jan';
                case 'February':
                    return 'Feb';
                case 'March':
                    return 'Mar';
                case 'April':
                    return 'Apr';
                case 'May':
                    return 'Mei';
                case 'June':
                    return 'Jun';
                case 'July':
                    return 'Jul';
                case 'August':
                    return 'Agu';
                case 'September':
                    return 'Sep';
                case 'October':
                    return 'Okt';
                case 'November':
                    return 'Nov';
                case 'December':
                    return 'Des';
                default:
                    return $englishMonth;
            }
        }

        $bulan_lalu_nama = $bulan_lalu ? convertToIndonesianMonth($bulan_lalu) : null;
        $bulan_sekarang_nama = $bulan_sekarang ? convertToIndonesianMonth($bulan_sekarang) : null;

       
        $tahun_sekarang = date('Y', strtotime($tagihan_sekarang->created_at));
    
        return view('layout.kwitansi', compact('tagihan_sekarang','tagihan_sebelumnya', 'bulan_lalu_nama', 'tahun_lalu', 'bulan_sekarang_nama', 'tahun_sekarang'));
        // return($kwitansi);
    }

    public function chart_admin_pemakaian()
    {
        $data = Tagihan::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(pemakaian) as total_pemakaian'))
        ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
        ->get();

        $labels = [];
        $totals = [];

        foreach ($data as $item) {
            $labels[] = \Carbon\Carbon::createFromDate($item->year, $item->month)->format('F Y');
            $totals[] = $item->total_pemakaian;
        }

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }

    public function chart_admin_tagihan()
    {
        $data = Tagihan::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(tagihan) as total_tagihan'))
        ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
        ->get();

        $labels = [];
        $totals = [];

        foreach ($data as $item) {
            $labels[] = \Carbon\Carbon::createFromDate($item->year, $item->month)->format('F Y');
            $totals[] = $item->total_tagihan;
        }

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }
    
    public function chart_admin_tagihan_year(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $data = Tagihan::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(tagihan) as total_tagihan'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->whereYear('created_at', '=', $year)
            ->get();

        $labels = [];
        $totals = [];

        foreach ($data as $item) {
            $labels[] = \Carbon\Carbon::createFromDate($item->year, $item->month)->format('F Y');
            $totals[] = $item->total_tagihan;
        }

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }
    public function chart_admin_pemakaian_year(Request $request)
    {
        $year = $request->query('year', date('Y')); // Ambil tahun dari query string, jika tidak ada, gunakan tahun saat ini

        $data = Tagihan::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(pemakaian) as total_pemakaian'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->whereYear('created_at', '=', $year) // Perbaikan disini
            ->get();

        $labels = [];
        $totals = [];

        foreach ($data as $item) {
            $labels[] = \Carbon\Carbon::createFromDate($item->year, $item->month)->format('F Y');
            $totals[] = $item->total_pemakaian;
        }

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }


    
    // public function sendWhatsAppMessages($id)
    // {
    //     $tagihan = Tagihan::where('id', $id)->first();
    //     $pemakaian = $tagihan->pemakaian;
    //     $tot_tagihan = $tagihan->tagihan;
    //     $pelanggan = $tagihan->kode_client;
    //     $numbers = Client::where('kode_client', $pelanggan)->pluck('no_whatsapp')->first();
    //     $messages = 'Pemakaian air anda bulan ini adalah '.$pemakaian.'. Berdasarkan pemakaian tersebut maka jumlah tagihan anda adalah Rp. '.$tot_tagihan.'.';
        
    //     $apiKey = "9PyhDM5EFbvzeDYEMpcbVUDpNPz416";
        
    //     $url = "https://api.dipsy.id/send-whatsapp";
        
    //     foreach ($numbers as $key => $number) {
    //         // Menghapus angka 0 di awal nomor WhatsApp
    //         $number = ltrim($number, '0');

    //         $payload = json_encode([
    //             "phone" => $number,
    //             "message" => $messages[$key]
    //         ]);
            
    //         $response = Http::withHeaders([
    //             "Content-Type" => "application/json",
    //             "X-API-Key" => $apiKey
    //         ])->post($url, json_decode($payload, true));
            
    //         if ($response->ok()) {
    //             // Pesan berhasil terkirim
    //             $tagihan->status_tagihan = 'Belum Dibayar';
    //             $tagihan->update();
    //             return redirect()->back();
    //             // echo "Pesan ke nomor $number berhasil dikirim: " . $response->body() . "<br>";
    //         } else {
    //             // Pesan gagal terkirim
    //             return "Pesan ke nomor $number gagal dikirim: " . $response->body() . "<br>";
    //         }
    //     }
    // }
    
    public function sendWhatsAppMessages($id)
    {
        $tagihan = Tagihan::where('id', $id)->first();
      
        $pemakaian = $tagihan->pemakaian;
        $bulan = date('F', strtotime($tagihan->created_at));
        $tot_tagihan = number_format($tagihan->tagihan, 0, ',', '.');
        $pelanggan = $tagihan->kode_client;
        $number = Client::where('kode_client', $pelanggan)->pluck('no_whatsapp')->first();
        $message = 'Pemakaian air anda bulan '.$bulan.' adalah *'.$pemakaian.'m³*. Berdasarkan pemakaian tersebut maka jumlah tagihan anda adalah Rp. *'.$tot_tagihan.'*.';
        
        // Menghapus angka 0 di awal nomor WhatsApp
        if (substr($number, 0, 1) == '0') {
            $number = '62' . substr($number, 1);
        }

        $data = [
            'api_key' => 'oUJIiaAsi3inWaHtdqC73hrtGVxEvQ',
            'sender' => '6287861529328', // Ganti dengan nomor WhatsApp pengirim
            'number' => $number,
            'message' => $message
        ];

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://wa.izam.men/send-message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        if ($response) {
            $tagihan->status_tagihan = 'Belum Dibayar';
            $tagihan->update();
            return redirect()->back();
        } else {
            // return redirect()->back()->with('error', 'Pesan gagal dikirim: ' . $response);
            return 'Pesan gagal dikirim: ' . $response;
        }
    }
  
  	public function Siarkan()
    {
      $tagihan = Tagihan::where('status_tagihan', 'Belum Disiarkan')->get();

      foreach ($tagihan as $data) {
          $pemakaian = $data->pemakaian;
          $bulan = date('F', strtotime($tagihan->created_at));
          $tot_tagihan = number_format($data->tagihan, 2, ',', '.');
          $pelanggan = $data->kode_client;
          $id_tagihan = $data->id;
          $numbers = Client::where('kode_client', $pelanggan)->pluck('no_whatsapp');

          foreach ($numbers as $number) {
              $message = 'Pemakaian air Anda bulan '.$bulan.'. adalah *'.$pemakaian.'m³*. Berdasarkan pemakaian tersebut, jumlah tagihan Anda adalah Rp. *'.$tot_tagihan.'*.';

              // Menghapus angka 0 di awal nomor WhatsApp
              if (substr($number, 0, 1) == '0') {
                  $number = '62' . substr($number, 1);
              }

              $data = [
                  'api_key' => 'oUJIiaAsi3inWaHtdqC73hrtGVxEvQ',
                  'sender' => '6287861529328', // Ganti dengan nomor WhatsApp pengirim
                  'number' => $number,
                  'message' => $message
              ];

              $response = Http::post('https://wa.izam.men/send-message', $data);

              if ($response->ok()) {
                  // Pesan berhasil terkirim
                  $dataTagihan = Tagihan::find($id_tagihan);
                  $dataTagihan->status_tagihan = 'Belum Dibayar';
                  $dataTagihan->save();
              } else {
                  // Pesan gagal terkirim
                  return 'error'.$response;
              }
          }
      }

      return redirect()->back()->with('success', 'Pesan berhasil dikirim: ');
    }

    public function apiGet(Request $request)
    {
        $user = User::where('token', $request->token)->first();
        if($user)
        {
            $tagihan = new Tagihan();
            $data = $tagihan->api_get();
            $responseData = [
                'data' => $data,
                'message' => "Data Tagihan",
                'status' => 200
            ];
            return response()->json($responseData);
        }
        else 
        {
            return response()->json('Unauthorized', 401);
        }
       
    }

    public function updateTagihan(Request $request)
    {
        $token = $request->input('token');
    
        // Cek apakah token ada
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
    
        // Cek kebenaran token dengan data user pada database
        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    
        // Lakukan validasi terhadap data yang diterima
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'kode_client' => 'required',
            'nomor_meter' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 400);
        }
    
        // Ambil nomor meter terakhir yang disimpan
        $pelanggan = $request->input('kode_client');

        $kode = Client::where('kode_client', $pelanggan)->first();

        if(!$kode) {
            return response()->json('Pelanggan tidak ditemukan', 400);
        }
        
        $nomorMeterTerakhir = Tagihan::where('kode_client', $pelanggan)
            ->orderBy('created_at', 'desc')
            ->pluck('nomor_meter')
            ->first();

        if(!$nomorMeterTerakhir) {
            return response()->json('Belum ada tagihan sebelumnya', 400);
        }

    
        // Ambil nomor meter terakhir yang disimpan
        $nomorMeterSekarang = $request->input('nomor_meter');
    
        // Ambil harga air
        $hargaAir = Air::first();
        $updateTagihan = Tagihan::find($request->id);
        $updateTagihan->nomor_meter = $nomorMeterSekarang;
        $updateTagihan->pemakaian = $nomorMeterSekarang - $nomorMeterTerakhir;
        $updateTagihan->tagihan = $updateTagihan->pemakaian * $hargaAir->harga;
        $updateTagihan->save();
    
        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }
}
