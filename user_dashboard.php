<?php 
session_start();
include('./admin/db_connect.php');

if(!isset($_SESSION['user_id'])){
  header("location:user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Dashboard - Event Management System</title>
  <?php include('./head.php') ?>
  <style>
    .stat-card {
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      color: white;
    }
    .stat-card.blue { background: #007bff; }
    .stat-card.green { background: #28a745; }
    .stat-card.orange { background: #fd7e14; }
    .stat-card.red { background: #dc3545; }
    .booking-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      background: white;
    }
    .booking-status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
    }
    .status-0 { background: #ffc107; color: #000; }
    .status-1 { background: #28a745; color: #fff; }
    .status-2 { background: #dc3545; color: #fff; }
  </style>
</head>
<body>
  <?php include('./header.php') ?>
  
  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-md-12">
        <h2>Welcome, <?php echo $user_name; ?>!</h2>
      </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mt-3">
      <div class="col-md-3">
        <div class="stat-card blue">
          <h4 id="total-bookings">0</h4>
          <p>Total Bookings</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card orange">
          <h4 id="pending-bookings">0</h4>
          <p>Pending</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card green">
          <h4 id="confirmed-bookings">0</h4>
          <p>Confirmed</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card red">
          <h4 id="cancelled-bookings">0</h4>
          <p>Cancelled</p>
        </div>
      </div>
    </div>
    
    <!-- My Bookings Section -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>My Event Bookings</h4>
          </div>
          <div class="card-body">
            <div id="bookings-list">
              <p class="text-center">Loading bookings...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include('./footer.php') ?>
  
  <script>
    function loadBookings() {
      $.ajax({
        url:'admin/ajax.php?action=get_my_bookings',
        method:'GET',
        success:function(resp){
          resp = JSON.parse(resp);
          var html = '';
          var total = 0, pending = 0, confirmed = 0, cancelled = 0;
          
          if(resp.length > 0){
            resp.forEach(function(booking){
              total++;
              var statusClass = 'status-' + booking.status;
              var statusText = '';
              if(booking.status == 0) { statusText = 'Pending'; pending++; }
              else if(booking.status == 1) { statusText = 'Confirmed'; confirmed++; }
              else { statusText = 'Cancelled'; cancelled++; }
              
              var eventDate = new Date(booking.schedule);
              var today = new Date();
              
              html += '<div class="booking-card">';
              html += '<div class="row">';
              html += '<div class="col-md-8">';
              html += '<h5>' + booking.event + '</h5>';
              html += '<p><strong>Venue:</strong> ' + (booking.venue || 'N/A') + '</p>';
              html += '<p><strong>Date:</strong> ' + eventDate.toLocaleDateString() + '</p>';
              html += '<p><strong>Booking Date:</strong> ' + new Date(booking.booking_date).toLocaleDateString() + '</p>';
              if(booking.notes) {
                html += '<p><strong>Notes:</strong> ' + booking.notes + '</p>';
              }
              html += '</div>';
              html += '<div class="col-md-4 text-right">';
              html += '<span class="booking-status ' + statusClass + '">' + statusText + '</span>';
              if(booking.status != 2 && booking.status != 1) {
                html += '<br><button class="btn btn-danger btn-sm mt-2" onclick="cancelBooking(' + booking.id + ')">Cancel Booking</button>';
              }
              html += '</div>';
              html += '</div>';
              html += '</div>';
            });
          } else {
            html = '<p class="text-center">No bookings yet. <a href="index.php">Browse Events</a></p>';
          }
          
          $('#bookings-list').html(html);
          $('#total-bookings').text(total);
          $('#pending-bookings').text(pending);
          $('#confirmed-bookings').text(confirmed);
          $('#cancelled-bookings').text(cancelled);
        }
      });
    }
    
    function cancelBooking(id) {
      if(confirm('Are you sure you want to cancel this booking?')) {
        start_load();
        $.ajax({
          url:'admin/ajax.php?action=cancel_booking',
          method:'POST',
          data:{id: id},
          success:function(resp){
            if(resp == 1) {
              alert_toast('Booking cancelled successfully!','success');
              loadBookings();
            } else {
              alert_toast('Failed to cancel booking!','danger');
            }
            end_load();
          }
        });
      }
    }
    
    $(document).ready(function(){
      loadBookings();
    });
  </script>
</body>
</html>
