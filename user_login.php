<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./admin/db_connect.php');
ob_start();
if(isset($_SESSION['user_id'])){
  header("location:index.php");
}
ob_end_flush();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>User Login - Event Management System</title>
  <?php include('./head.php') ?>
  <style>
    body {
      background: #f5f5f5;
    }
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .form-group label {
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="login-container">
      <h3 class="text-center mb-4">User Login</h3>
      <form id="user-login-form">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </div>
        <div class="text-center">
          <p>Don't have an account? <a href="user_register.php">Register here</a></p>
        </div>
      </form>
    </div>
  </div>

  <script>
    $('#user-login-form').submit(function(e){
      e.preventDefault();
      start_load();
      $.ajax({
        url:'admin/ajax.php?action=user_login',
        method:'POST',
        data:$(this).serialize(),
        success:function(resp){
          if(resp == 1){
            alert_toast("Login successful!",'success');
            setTimeout(function(){
              location.href = 'index.php';
            },1000);
          }else{
            alert_toast("Invalid email or password!",'danger');
            end_load();
          }
        }
      });
    });
  </script>
</body>
</html>
