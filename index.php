<?php
/*
 * Serves as the index page for a storage unit website. 
 */ 

ini_set('session.cookie_httponly', 1);
session_start();

$LIB_PATH = '/usr/local/###/';
$CONFIG_FILE_PATH = "/var/www/###/site.conf";
require_once($LIB_PATH . 'sysconfig.php');
require_once($LIB_PATH . 'db.inc');
require_once($LIB_PATH . 'session.inc');
$conf = get_system_config();
$db = connect_to_db($conf['db'], $conf['db_database'], $conf['db_user'], $conf['db_password']);

if (is_sid_in_PHP_session()) {
  $sid = get_sid_from_PHP_session();
  $sid = test_input($sid);
}

$session_parms = load_session_parameters($sid, $conf, $db);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "head.inc"; ?>
</head>

<body>
  <?php include "nav.inc"; ?>

  <!-- Page Content -->
  <div class="container">
    <!-- Jumbotron Header -->
    <header class="jumbotron my-4" style="background-image: url(/storage-unit.jpg); background-repeat: no-repeat;background-size:cover">
      <h1 class="display-3" style="color:white">Progressive Store and Lock</h1>
      <p class="lead" style="color:white">We offer secure, conventient storage at competitive rates.</p>
      <a href="/register.php" class="btn btn-primary btn-lg">Move In Today!</a>
    </header>

    <!-- Page Features -->
    <div class="row text-center">

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="storage-unit.jpg" alt="">
          <div class="card-body">
            <h4 class="card-title">6 x 10</h4>
            <p class="card-text">6 feet by 10 feet</p>
          </div>
          <div class="card-footer">
            <a href='/reserve_unit_email_prompt.php?unit_size="6x10"' class="btn btn-primary">Reserve my Unit!</a>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="storage-unit.jpg" alt="">
          <div class="card-body">
            <h4 class="card-title">10 x 10</h4>
            <p class="card-text">10 feet by 10 feet</p>
          </div>
          <div class="card-footer">
            <a href='/reserve_unit_email_prompt.php?unit_size="10x10"' class="btn btn-primary">Reserve my Unit!</a>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="storage-unit.jpg" alt="">
          <div class="card-body">
            <h4 class="card-title">10 x 15</h4>
            <p class="card-text">10 feet by 15 feet</p>
          </div>
          <div class="card-footer">
            <a href='/reserve_unit_email_prompt.php?unit_size="10x15"' class="btn btn-primary">Reserve my Unit!</a>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="storage-unit.jpg" alt="">
          <div class="card-body">
            <h4 class="card-title">10 x 20</h4>
            <p class="card-text">10 feet by 20 feet</p>
          </div>
          <div class="card-footer">
            <a href='/reserve_unit_email_prompt.php?unit_size="10x20"' class="btn btn-primary">Reserve my Unit!</a>
          </div>
        </div>
      </div>

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->

  <?php include "footer.inc"; ?>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>
