<?php 
include 'admin/db_connect.php'; 

// Get filter type from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$where = "WHERE e.type = 1";
if($filter == 'upcoming') {
    $where .= " AND date_format(e.schedule,'%Y-%m-%d') > '".date('Y-m-d')."'";
} elseif($filter == 'ongoing') {
    $where .= " AND date_format(e.schedule,'%Y-%m-%d') <= '".date('Y-m-d')."' AND date_format(DATE_ADD(e.schedule, INTERVAL 1 DAY),'%Y-%m-%d') >= '".date('Y-m-d')."'";
} elseif($filter == 'completed') {
    $where .= " AND date_format(DATE_ADD(e.schedule, INTERVAL 1 DAY),'%Y-%m-%d') < '".date('Y-m-d')."'";
}

$query = "SELECT e.*,v.venue FROM events e left join venue v on v.id=e.venue_id $where order by unix_timestamp(e.schedule) asc";
$event = $conn->query($query);
?>
<style>
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.event-list{
cursor: pointer;
}
span.hightlight{
    background: yellow;
}
.banner{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 26vh;
        width: calc(30%);
    }
    .banner img{
        width: calc(100%);
        height: calc(100%);
        cursor :pointer;
    }
.event-list{
cursor: pointer;
border: unset;
flex-direction: inherit;
}

.event-list .banner {
    width: calc(40%)
}
.event-list .card-body {
    width: calc(60%)
}
.event-list .banner img {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    min-height: 50vh;
}
span.hightlight{
    background: yellow;
}
.banner{
   min-height: calc(100%)
}
.event-status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 10px;
}
.status-upcoming { background: #007bff; color: white; }
.status-ongoing { background: #28a745; color: white; }
.status-completed { background: #6c757d; color: white; }
.search-box {
    max-width: 400px;
    margin: 0 auto 20px;
}
</style>
        <header class="masthead">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Welcome to <?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : 'Event Management System'; ?></h3>
                        <hr class="divider my-4" />

                    <div class="col-md-12 mb-2 justify-content-center">
                    </div>                        
                    </div>
                    
                </div>
            </div>
        </header>
            <div class="container mt-3 pt-2">
                <!-- Search Box -->
                <div class="search-box">
                    <input type="text" id="search-input" class="form-control" placeholder="Search events...">
                </div>
                
                <!-- Filter Buttons -->
                <div class="text-center mb-4">
                    <a href="?page=home&filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">All Events</a>
                    <a href="?page=home&filter=upcoming" class="btn <?php echo $filter == 'upcoming' ? 'btn-primary' : 'btn-outline-primary'; ?>">Upcoming</a>
                    <a href="?page=home&filter=ongoing" class="btn <?php echo $filter == 'ongoing' ? 'btn-primary' : 'btn-outline-primary'; ?>">Ongoing</a>
                    <a href="?page=home&filter=completed" class="btn <?php echo $filter == 'completed' ? 'btn-primary' : 'btn-outline-primary'; ?>">Completed</a>
                </div>
                
                <h4 class="text-center text-white">
                    <?php 
                    if($filter == 'all') echo 'All Events';
                    elseif($filter == 'upcoming') echo 'Upcoming Events';
                    elseif($filter == 'ongoing') echo 'Ongoing Events';
                    elseif($filter == 'completed') echo 'Completed Events';
                    ?>
                </h4>
                <hr class="divider">
                
                <div id="events-container">
                <?php
                if($event->num_rows > 0):
                while($row = $event->fetch_assoc()):
                    $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
                    unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                    $desc = strtr(html_entity_decode($row['description']),$trans);
                    $desc=str_replace(array("<li>","</li>"), array("",","), $desc);
                    
                    // Determine event status
                    $schedule = strtotime($row['schedule']);
                    $now = time();
                    $oneDayLater = $schedule + 86400;
                    
                    if($now < $schedule) {
                        $status = 'upcoming';
                        $statusLabel = 'Upcoming';
                    } elseif($now >= $schedule && $now < $oneDayLater) {
                        $status = 'ongoing';
                        $statusLabel = 'Ongoing';
                    } else {
                        $status = 'completed';
                        $statusLabel = 'Completed';
                    }
                ?>
                <div class="card event-list" data-id="<?php echo $row['id'] ?>">
                     <div class='banner'>
                        <?php if(!empty($row['banner'])): ?>
                            <img src="admin/assets/uploads/<?php echo($row['banner']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row  align-items-center justify-content-center text-center h-100">
                            <div class="">
                                <span class="event-status status-<?php echo $status; ?>"><?php echo $statusLabel; ?></span>
                                <h3><b class="filter-txt"><?php echo ucwords($row['event']) ?></b></h3>
                                <div><small><p><b><i class="fa fa-calendar"></i> <?php echo date("F d, Y h:i A",strtotime($row['schedule'])) ?></b></p></small></div>
                                <div><small><p><b><i class="fa fa-map-marker"></i> <?php echo isset($row['venue']) ? $row['venue'] : 'TBA'; ?></b></p></small></div>
                                <hr>
                                <larger class="truncate filter-txt"><?php echo strip_tags($desc) ?></larger>
                                <br>
                                <hr class="divider"  style="max-width: calc(80%)">
                                <button class="btn btn-primary float-right read_more" data-id="<?php echo $row['id'] ?>">Read More</button>
                            </div>
                        </div>
                        

                    </div>
                </div>
                <br>
                <?php 
                endwhile;
                else:
                ?>
                <div class="text-center text-white">
                    <p>No events found.</p>
                </div>
                <?php endif; ?>
                </div>
            </div>


<script>
     $('.read_more').click(function(){
         location.href = "index.php?page=view_event&id="+$(this).attr('data-id')
     })
     $('.banner img').click(function(){
        viewer_modal($(this).attr('src'))
    })
$('#filter').keyup(function(e){
        var filter = $(this).val()

        $('.card.event-list .filter-txt').each(function(){
            var txto = $(this).html();
            txt = txto
            if((txt.toLowerCase()).includes((filter.toLowerCase())) == true){
                $(this).closest('.card').toggle(true)
            }else{
                $(this).closest('.card').toggle(false)
               
            }
        })
    })
    
    // Real-time search functionality
    $('#search-input').keyup(function(e){
        var search = $(this).val().toLowerCase();
        
        $('.card.event-list').each(function(){
            var eventName = $(this).find('.filter-txt').text().toLowerCase();
            if(eventName.includes(search)){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
        
        if($('.card.event-list:visible').length == 0) {
            if($('#no-results').length == 0) {
                $('#events-container').append('<div id="no-results" class="text-center text-white"><p>No events found matching your search.</p></div>');
            }
        } else {
            $('#no-results').remove();
        }
    })
</script>
