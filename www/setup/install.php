<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flannel Framework Install Script</title>

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
                        <a class="nav-link" href="#step-5">
                            5. User Creation
                        </a>
                    </li>
                </ul>

                <form id="installForm" action="/setup/installer.php" method="post">
                    <div class="tab-content">
                        <!-- Step 1 -->
                        <div id="step-1" class="tab-pane" role="tabpanel">
                            <div class="form-group row mb-3 mt-3">
                                <label class="col-sm-2" for="mode">Mode</label>
                                <select class="form-control col-sm-10" name="mode" id="mode" required>
                                    <option value="website">Website</option>
                                    <option value="api">API</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Base URL" name="base_url" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-globe"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Config Name" name="config_name"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-file-alt"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div id="step-2" class="tab-pane" role="tabpanel">
                            <div class="form-group row mb-3 mt-3">
                                <label class="col-sm-2" for="enableSentry">Enable Sentry</label>
                                <select class="form-control col-sm-10" name="enable_sentry" id="enableSentry" required>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                            <div class="input-group mb-3" id="sentryUrlBlock">
                                <input type="text" class="form-control" placeholder="DNS URL" name="sentry_url"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-globe"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3" id="sentryEnvironmentBlock">
                                <input type="text" class="form-control" placeholder="Environment"
                                       name="sentry_environment" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-home"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div id="step-3" class="tab-pane" role="tabpanel">
                            <div class="input-group mt-3 mb-3">
                                <input type="text" class="form-control" placeholder="Host" name="mysql_host" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-server"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Username" name="mysql_username"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" placeholder="Password"
                                       name="mysql_password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Database" name="mysql_database"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-database"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Step 4 -->
                        <div id="step-4" class="tab-pane" role="tabpanel">
                            <div class="form-group row mb-3 mt-3">
                                <label class="col-sm-2" for="cachingType">Caching Source</label>
                                <select class="form-control col-sm-10" name="caching_type" id="cachingType" required>
                                    <option value="file">File</option>
                                    <option value="Redis">Redis</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Save Path"
                                       name="caching_save_path" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-save"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Step 5 -->
                        <div id="step-5" class="tab-pane" role="tabpanel">
                            <div class="input-group mt-3 mb-3">
                                <input type="text" class="form-control" placeholder="Admin Email"
                                       name="admin_email" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" placeholder="Admin Password"
                                       name="admin_password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="AvidBase Account ID"
                                       name="admin_account_id" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-users"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="AvidBase API Key"
                                       name="admin_api_key" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-key"></span>
                                    </div>
                                </div>
                            </div>
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

