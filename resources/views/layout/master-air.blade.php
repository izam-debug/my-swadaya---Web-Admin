@extends('template')
@section('content')
<div class="card">
    <div class="card-header"><h2>Harga Air</h2></div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        

        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Data Master harga air</h4>
            <p>Harga air yang berlaku digunakan untuk menghitung tagihan pemakaian air pelanggan.</p>
           
          </div>
        {{-- <div style="display: flex; justify-content: center; align-items: center; height: 50vh;"> --}}
            <form action="/update-harga-air" method="POST">
            @csrf
            <input type="number" name="harga_air" class="form-control" id="harga-air" step="500" style="height: 50px;" oninput="validateHarga()" value="{{$harga}}" disabled>
            @error('harga_air')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            <button type="button" class="btn btn-success mt-2 ubah-harga"><i class="bi bi-pencil-fill"></i> Ubah</button>
            <button type="submit" class="btn btn-primary mt-2 simpan-harga" style="display: none;"><i class="bi bi-floppy-fill"></i> Simpan</button>
            </form>
        {{-- </div> --}}
    </div>
</div>
@endsection
@push('script')
<script>
    $(document).ready(function () {
        $('.ubah-harga').click(function () {
            $("#harga-air").prop("disabled", false);
            $("#harga-air").focus();
            $(this).hide();
            $('.simpan-harga').show();
        });
        
        $('.simpan-harga').click(function () {
            var harga = $('#harga-air').val();
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/update-harga-air',
                type: 'POST',
                data: { harga },
                success: function(response) {
                   
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    });
</script>
<script>
    function validateHarga() {
        var inputElement = document.getElementById('harga-air');
        var inputValue = inputElement.value;

        // Hapus karakter selain digit dari input
        var numericValue = inputValue.replace(/\D/g, '');

        // Setel nilai input setelah validasi
        inputElement.value = numericValue;
        if (inputValue.length > 5) {
            inputElement.value = inputValue.slice(0, 5); // Potong input jika melebihi 5 digit
        }
    }
</script>

@endpush