@extends('template')
@section('css')
<link rel="stylesheet" href="http://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Tagihan Pelanggan (Belum Disiarkan)</h2>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-body">
        <div class="row">
           <div class="col-md-12 d-flex justify-content-end mb-2">
          		<form action="/siarkan" method="post" style="display: inline;">
                    @csrf
                    <button class="btn btn-primary" type="submit" title="Kirimkan Tagihan"><i class="bi bi-megaphone-fill"></i></button>
                </form> 
          </div>
        </div>
        <table class="table table-striped" style="width: 100%;" id="tabel_pelanggan">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Nama Pelanggan</td>
                    <td>Pemakaian</td>
                    <td>Total Tagihan</td>
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
                        <form action="/broadcast/{{$tg->id}}" method="post" style="display: inline;">
                            @csrf
                            <button class="btn btn-primary" type="submit" title="Kirimkan Tagihan"><i class="bi bi-megaphone-fill"></i></button>
                        </form>                        
                    </td>
                    {{-- <td>
                        <button class="btn btn-primary btn-rounded btn-fw update_bayar" style="color: white;" onclick="confirmDelete({{ $tg->id }})" title="Tagihan Dibayar"><i class="bi bi-cash-coin"></i></button>
                    </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!--Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel"><i class="mdi mdi-account-plus"></i>Tambah Pelanggan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/add/client" method="POST">
            @csrf
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label ">Nama</label>
                <input name="nama" type="text" class="form-control @error('nama') is-invalid @enderror">
                @error('nama')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Username</label>
                <input type="text" name="username" id="username" oninput="validateUsername()" class="form-control @error('username') is-invalid @enderror" placeholder="maksimal 10 karakter">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Password</label>
                <input type="text" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">No Whatsapp</label>
                <input type="tel" name="no_whatsapp" id="no_whatsapp" oninput="validateWhatsAppNumber()" class="form-control @error('no_whatsapp') is-invalid @enderror" placeholder="08512345678">
                @error('no_whatsapp')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Simpan</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<!--end Add Modal -->

<!--Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel"><i class="mdi mdi-account-plus"></i>Tambah Pelanggan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/update/client" method="POST">
            @csrf
            <input type="hidden" name="id_client" id="id_client">
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label ">Nama</label>
                <input name="nama" id="edit_nama" type="text" class="form-control @error('nama') is-invalid @enderror">
                @error('nama')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Username</label>
                <input type="text" name="username" id="edit_username" oninput="validateEditUsername()" class="form-control @error('username') is-invalid @enderror" placeholder="maksimal 10 karakter">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Password</label>
                <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" value="pelanggan123">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">No Whatsapp</label>
                <input type="tel" name="no_whatsapp" id="edit_no_whatsapp" oninput="validateEditWhatsAppNumber()" class="form-control @error('no_whatsapp') is-invalid @enderror" placeholder="08512345678">
                @error('no_whatsapp')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2"></i> Simpan</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<!--end Edit Modal -->
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
<script>
    function validateWhatsAppNumber() {
        var inputElement = document.getElementById('no_whatsapp');
        var inputValue = inputElement.value;

        // Hapus karakter selain digit dari input
        var numericValue = inputValue.replace(/\D/g, '');

        // Setel nilai input setelah validasi
        inputElement.value = numericValue;
    }
    function validateWhatsEditAppNumber() {
        var inputElement = document.getElementById('edit_no_whatsapp');
        var inputValue = inputElement.value;

        // Hapus karakter selain digit dari input
        var numericValue = inputValue.replace(/\D/g, '');

        // Setel nilai input setelah validasi
        inputElement.value = numericValue;
    }
    function validateUsername() {
            var inputElement = document.getElementById('username');
            var inputValue = inputElement.value;

            // Hapus karakter selain huruf, angka, dan underscore dari input
            var validatedValue = inputValue.replace(/[^a-zA-Z0-9_]/g, '');

            // Batasi panjang input menjadi maksimal 10 karakter
            validatedValue = validatedValue.substring(0, 10);

            // Setel nilai input setelah validasi
            inputElement.value = validatedValue;
    }
    function validateEditUsername() {
            var inputElement = document.getElementById('edit_username');
            var inputValue = inputElement.value;

            // Hapus karakter selain huruf, angka, dan underscore dari input
            var validatedValue = inputValue.replace(/[^a-zA-Z0-9_]/g, '');

            // Batasi panjang input menjadi maksimal 10 karakter
            validatedValue = validatedValue.substring(0, 10);

            // Setel nilai input setelah validasi
            inputElement.value = validatedValue;
    }
    function confirmDelete(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, arahkan ke rute penghapusan
                window.location.href = '/clients/' + id + '/delete';
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