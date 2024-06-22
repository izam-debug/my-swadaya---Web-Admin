@extends('template')
@section('css')
<link rel="stylesheet" href="http://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Tagihan Pelanggan (Sudah Disiarkan)</h2>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-body">
        {{-- <div class="row">
            <div class="col-md-12 d-flex justify-content-end mb-2"><button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-megaphone-fill"></i></button></div>
        </div> --}}
        <table class="table table-striped" style="width: 100%;" id="tabel_pelanggan">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Nama Pelanggan</td>
                    <td>Pemakaian</td>
                    <td>Total Tagihan</td>
                    <td>Status Tagihan</td>
                    <td>Aksi</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($tagihan as $tg)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$tg->nama_client}}</td>
                    <td>{{$tg->pemakaian}}</td>
                    <td>{{number_format($tg->tagihan, 0, ',', '.')}}</td>
                    <td>
                        @if ($tg->status_tagihan == "Dibayar")
                        <label class="badge badge-success" style="color: black;">Sudah Dibayar</label>
                        @else
                        <label class="badge badge-danger" style="color: black;">Belum Dibayar</label>
                        @endif
                    </td>
                    <td>
                        @if ($tg->status_tagihan == "Belum Dibayar")
                        <button class="btn btn-primary btn-rounded btn-fw update_bayar" style="color: white;" onclick="confirmBayar({{ $tg->id }})" title="Tagihan Dibayar"><i class="bi bi-cash-coin"></i></button>
                        @else
                        <a href="/kwitansi/{{$tg->id}}" target="_blank"><button class="btn btn-success btn-rounded btn-fw cetak_kwitansi" style="color: white;" title="Cetak Kwitansi"><i class="bi bi-printer"></i></button></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
<script>
    function confirmBayar(id) {
        Swal.fire({
            title: 'Konfirmasi Bayar',
            text: 'Apakah Anda yakin ingin mengubah status data ini menjadi DIBAYAR?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yakin!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, arahkan ke rute penghapusan
                window.location.href = '/tagihan/' + id + '/bayar';
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $('.edit_client').click(function () {
            var clientId = $(this).data('client-id');

            // Ajax request untuk mengambil data client berdasarkan ID
            $.ajax({
                url: '/clients/' + clientId + '/edit',
                method: 'GET',
                success: function (data) {
                    $('#id_client').val(data.id);
                    $('#edit_nama').val(data.nama_client);
                    $('#edit_username').val(data.username);
                    $('#edit_no_whatsapp').val(data.no_whatsapp);
                    $('#editModal').modal('show');
                    console.log(data);
                },
                error: function (error) {
                    console.error('Error fetching client data:', error);
                }
            });
        });
    });

    let table = new DataTable('#tabel_pelanggan');
</script>
@endpush