<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KWITANSI TAGIHAN AIR</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
        }
        .container {
            width: 48mm;
            /* margin: auto; */
            padding-right: 1px;
            margin-right: 2px;
            margin-left: 1px;
            padding-left: 2px;
            margin-top: 1px;
            padding-top: 1px;
        }
        .header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        .info {
            margin-bottom: 5px;
            text-align: center;
        }
        .info span {
            display: block;
        }
        .details {
            margin-top: 10px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #000;
            padding: 5px;
        }
        .footer {
            padding-top: 1spx;
            text-align: center;
            /* line-height: 2px; */
        }

        .footer p {
            padding: 0px;
        }
        .bio-table tr td{
            line-height: 0px !important;
            margin: 0px !important;
            height: 10px !important;
            padding: 0px ;
        }
        /* .bio-table {
            display: flex;
            flex-direction: column;
        }
        .bio-table tr {
            display: flex;
        }
        .bio-table tr td {
            flex: 1;
            /* line-height: 1px; */
            /* padding: 0;
        } */ 
        .bio-table p {
            margin: 0 !important;
            padding: 0 !important;
            /* margin-left: 2px !important; */
            line-height: 0 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">KWITANSI TAGIHAN AIR</div>
        <div class="info">
            <span><b>Air Swadaya <br> Desa Sidorejo</b></span>
        </div>
        <div class="bio">
            <table class="bio-table">
                <tr>
                    <td>
                        <p><b>Id Pelanggan<b></p>
                    </td>
                    <td>
                        <p><b>: {{$tagihan_sekarang->kode_client}}</b></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><b>Nama </b></p>
                    </td>
                    <td>
                        <p><b>: {{ucfirst($tagihan_sekarang->nama_client)}}</b></p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>M</th>
                        {{-- <th>(M<sup>3</sup>)</th> --}}
                        {{-- <th>Total Tagihan (Rp)</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>{{$bulan_lalu_nama}} - {{$tahun_lalu}}</b></td>
                        <td style="text-align: right;"><b>{{$tagihan_sebelumnya->nomor_meter}}</b></td>
                        {{-- <td>{{$tagihan_sebelumnya->pemakaian}}</td> --}}
                        {{-- <td>Rp. {{number_format($tagihan_sebelumnya->tagihan, 0, ',', '.')}}</td> --}}
                    </tr>
                    <tr>
                        <td><b>{{$bulan_sekarang_nama}} - {{$tahun_sekarang}}</b></td>
                        <td style="text-align: right;"><b>{{$tagihan_sekarang->nomor_meter}}</b></td>
                        {{-- <td>{{$tagihan_sekarang->pemakaian}}</td> --}}
                        {{-- <td>Rp. {{number_format($tagihan_sebelumnya->tagihan, 0, ',', '.')}}</td> --}}
                    </tr>
                    <tr>
                        <td colspan=2 style="border: none; font-weight: bold;">*==================*</td>
                    </tr>
                    <tr>
                        <td><b>Pemakaian</b></td>
                        <td><b>{{$tagihan_sekarang->pemakaian}} M<sup>3</sup></b></td>
                    </tr>
                    <tr>
                        <td><b>Tagihan</b></td>
                        <td><b> {{number_format($tagihan_sekarang->tagihan, 0, ',', '.')}}<b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <p><b>Terima kasih atas pembayaran Anda.</b></p>
        </div>
    </div>
  
  <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }); // Mengatur delay sebelum mencetak (dalam milidetik)
        };
    </script>
</body>
</html>
