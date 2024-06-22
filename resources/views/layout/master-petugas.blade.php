@extends('template')
@section('css')
<link rel="stylesheet" href="http://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Petugas</h2>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-end mb-2"><button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#addModal"><i class="mdi mdi-account-plus"></i></button></div>
        </div>
        <table class="table table-striped" style="width: 100%;" id="tabel_petugas">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Nama</td>
                    <td>Username</td>
                    <td>Status</td>
                    <td>Aksi</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($petugas as $pt)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$pt->name}}</td>
                    <td>{{$pt->username}}</td>
                    <td>
                        @if($pt->status == 'aktif')
                        <label class="badge badge-success" style="color: black;">Aktif</label>
                        @else
                        <label class="badge badge-danger" style="color: black;">Tidak Aktif</label>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-success btn-rounded btn-fw edit_petugas" title="edit" style="color: white;" data-toggle="modal" data-target="#editClientModal" data-petugas-id="{{ $pt->id }}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-danger btn-rounded btn-fw delete_client" title="edit" style="color: white;" onclick="confirmDelete({{ $pt->id }})"><i class="bi bi-trash3-fill"></i></button>
                    </td>
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
          <form action="/add/petugas" method="POST">
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
                <label for="exampleInputEmail1" class="form-label">Status</label>
                <select class="form-control" name="status" id="status">
                    <option selected>Aktif</option>
                    <option>Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
          <form action="/update/petugas" method="POST">
            @csrf
            <input type="hidden" name="id_petugas" id="id_petugas">
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
                <input type="text" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Status</label>
                <select class="form-control" name="status" id="status">
                    <option selected>Aktif</option>
                    <option>Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                window.location.href = '/petugas/' + id + '/delete';
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $('.edit_petugas').click(function () {
            var petugasId = $(this).data('petugas-id');

            // Ajax request untuk mengambil data client berdasarkan ID
            $.ajax({
                url: '/petugas/' + petugasId + '/edit',
                method: 'GET',
                success: function (data) {
                    $('#id_petugas').val(data.id);
                    $('#edit_nama').val(data.name);
                    $('#edit_username').val(data.username);
                    $('#edit_status').val(data.status);
                    $('#editModal').modal('show');
                    console.log(data);
                },
                error: function (error) {
                    console.error('Error fetching client data:', error);
                }
            });
        });
    });

    let table = new DataTable('#tabel_petugas');
</script>
@endpush