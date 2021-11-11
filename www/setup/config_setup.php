<?php
$configDirectories = array_diff(scandir("../../conf"), array('..', '.', '.gitignore', 'conf.php.template', 'default.php', 'conf.php'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flannel Framework Config Setup</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
            <h5 class="text-center">Select Config</h5>
            <form method="post" action="/setup/configure.php">
                <div class="form-group mb-3 mt-3">
                    <select class="form-control" name="config_file" id="configFile" required>
                        <?php foreach ($configDirectories as $key => $directory): ?>
                            <option value="<?php echo $directory; ?>"><?php echo $directory; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="/js/admin/plugins/jquery/jquery.min.js"></script>
<!-- jQuery Validate -->
<script src="/js/admin/plugins/jquery-validate/jquery.validate.min.js"></script>
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

