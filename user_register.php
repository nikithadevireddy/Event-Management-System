<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./admin/db_connect.php');
ob_start();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>User Registration - Event Management System</title>
  <?php include('./head.php') ?>
  <style>
    body {
      background: #f5f5f5;
    }
    .register-container {
      max-width: 500px;
      margin: 50px auto;
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
    <div class="register-container">
      <h3 class="text-center mb-4">User Registration</h3>
      <form id="user-register-form">
        <div class="form-group">
          <label for="fullname">Full Name</label>
          <input type="text" id="fullname" name="fullname" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="address">Address</label>
          <textarea id="address" name="address" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block">Register</button>
        </div>
        <div class="text-center">
          <p>Already have an account? <a href="user_login.php">Login here</a></p>
        </div>
      </form>
    </div>
  </div>

  <script>
    $('#user-register-form').submit(function(e){
      e.preventDefault();
      
      // Check password match
      if($('#password').val() != $('#confirm_password').val()){
        alert_toast("Passwords do not match!",'danger');
        return false;
      }
      
      start_load();
      $.ajax({
        url:'admin/ajax.php?action=register_user',
        method:'POST',
        data:$(this).serialize(),
        success:function(resp){
          if(resp == 1){
            alert_toast("Registration successful! Please login.",'success');
            setTimeout(function(){
              location.href = 'user_login.php';
            },1500);
          }else if(resp == 2){
            alert_toast("Email already registered!",'danger');
            end_load();
          }else{
            alert_toast("Registration failed!",'danger');
            end_load();
          }
        }
      });
    });
  </script>
</body>
</html>
