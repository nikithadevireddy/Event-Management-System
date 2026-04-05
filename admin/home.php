<?php include 'db_connect.php' ?>
<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    color: #ffffff96;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
	#imagesCarousel img{
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
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
	.stat-card.purple { background: #6f42c1; }
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>	
                </div>
            </div>      			
        </div>
    </div>
    
    <!-- Dashboard Statistics -->
    <div class="row mt-3 ml-3 mr-3">
        <div class="col-md-3">
            <div class="stat-card blue">
                <h4 id="total-events">0</h4>
                <p>Total Events</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card green">
                <h4 id="upcoming-events">0</h4>
                <p>Upcoming Events</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card orange">
                <h4 id="ongoing-events">0</h4>
                <p>Ongoing Events</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card red">
                <h4 id="completed-events">0</h4>
                <p>Completed Events</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-3 ml-3 mr-3">
        <div class="col-md-3">
            <div class="stat-card purple">
                <h4 id="total-venues">0</h4>
                <p>Total Venues</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card blue">
                <h4 id="total-users">0</h4>
                <p>Registered Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card green">
                <h4 id="total-bookings">0</h4>
                <p>Total Bookings</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card orange">
                <h4 id="pending-bookings">0</h4>
                <p>Pending Bookings</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Load dashboard statistics
    $(document).ready(function(){
        loadStats();
    });
    
    function loadStats() {
        $.ajax({
            url:'ajax.php?action=get_dashboard_stats',
            method:'GET',
            success:function(resp){
                resp = JSON.parse(resp);
                $('#total-events').text(resp.total_events || 0);
                $('#upcoming-events').text(resp.upcoming_events || 0);
                $('#ongoing-events').text(resp.ongoing_events || 0);
                $('#completed-events').text(resp.completed_events || 0);
                $('#total-venues').text(resp.total_venues || 0);
                $('#total-users').text(resp.total_users || 0);
                $('#total-bookings').text(resp.total_bookings || 0);
                $('#pending-bookings').text(resp.pending_bookings || 0);
            }
        });
    }
</script>
<script>
	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Unknow tracking id.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }
</script>