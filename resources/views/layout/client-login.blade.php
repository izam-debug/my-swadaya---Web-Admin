<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Star Admin2 </title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/typicons/typicons.css">
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="{{asset('admin')}}/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{asset('admin')}}/assets/css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('admin')}}/assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <center>
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Seal_of_Magetan_Regency.svg/442px-Seal_of_Magetan_Regency.svg.png" alt="logo">
                </center>
              </div>
              <h4>Air Swadaya Desa Sidorejo</h4>
              <h6 class="fw-light">Sign in untuk melanjutkan</h6>
              @if (session()->has('loginError'))

              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('loginError') }}
            
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
              <form class="pt-3" action="/client/login" method="POST">
                @csrf
                <div class="form-group">
                  <input type="text" name="username" class="form-control form-control-lg" id="exampleInputEmail1"
                    placeholder="Username" required>
                </div>
                <div class="form-group">
                  <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1"
                    placeholder="Password" required>
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>

  
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{asset('admin')}}/assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="{{asset('admin')}}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{asset('admin')}}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset('admin')}}/assets/js/off-canvas.js"></script>
  <script src="{{asset('admin')}}/assets/js/hoverable-collapse.js"></script>
  <script src="{{asset('admin')}}/assets/js/template.js"></script>
  <script src="{{asset('admin')}}/assets/js/settings.js"></script>
  <script src="{{asset('admin')}}/assets/js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>