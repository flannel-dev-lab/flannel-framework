<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flannel Framework Install Script</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/css/admin/plugins/fontawesome/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="/css/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/css/admin/adminlte.min.css">
  <!-- Smartwizard style -->
  <link rel="stylesheet" href="/css/admin/plugins/smartwizard/smart_wizard_all.min.css">
  <link rel="stylesheet" href="/css/admin/style.css">
</head>
<body class="hold-transition login-page">
<div class="login-box install-box">
  <div class="login-logo">
     <img src="/img/admin/flannel-logo.png" width="50px" alt="Flannel Dev Lab Logo">
    <a href="/admin/auth/login"><b>Flannel</b> Framework</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">

<div id="smartwizard">
    <ul class="nav">
       <li>
           <a class="nav-link" href="#step-1">
              1. Base Config
           </a>
       </li>
       <li>
           <a class="nav-link" href="#step-2">
              2. Error Handling
           </a>
       </li>
       <li>
           <a class="nav-link" href="#step-3">
              3. MySQL
           </a>
       </li>
       <li>
           <a class="nav-link" href="#step-4">
              4. Caching
           </a>
       </li>
       <li>
           <a class="nav-link" href="#step-4">
              5. User Creation
           </a>
       </li>
    </ul>
 
      <form action="/setup/installer.php" method="post">
    <div class="tab-content">
        <!-- Step 1 -->
       <div id="step-1" class="tab-pane" role="tabpanel">
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
       </div>
        <!-- Step 2 -->
       <div id="step-2" class="tab-pane" role="tabpanel">
          Step content
       </div>
        <!-- Step 3 -->
       <div id="step-3" class="tab-pane" role="tabpanel">
          Step content
       </div>
        <!-- Step 4 -->
       <div id="step-4" class="tab-pane" role="tabpanel">
          Step content
       </div>
        <!-- Step 5 -->
       <div id="step-5" class="tab-pane" role="tabpanel">
          Step content
       </div>
    </div>
      </form>
</div>


      <p class="login-box-msg"></p>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="/js/admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/js/admin/plugins/bootstrap/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/js/admin/adminlte.js"></script>
<!-- SmartWizard -->
<script src="/js/admin/plugins/smartwizard/jquery.smartWizard.min.js"></script>
<!-- Install JS -->
<script src="/js/admin/install.js"></script>
</body>
</html>

