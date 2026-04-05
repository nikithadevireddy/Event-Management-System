<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = '$type' ";
		if($type == 1)
			$establishment_id = 0;
		$data .= ", establishment_id = '$establishment_id' ";
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = 3";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['settings'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_venue(){
		extract($_POST);
		$data = " venue = '$venue' ";
		$data .= ", address = '$address' ";
		$data .= ", description = '$description' ";
		$data .= ", rate = '$rate' ";
		if(empty($id)){
			//echo "INSERT INTO arts set ".$data;
			$save = $this->db->query("INSERT INTO venue set ".$data);
			if($save){
				$id = $this->db->insert_id;
				$folder = "assets/uploads/venue_".$id;
				if(is_dir($folder)){
					$files = scandir($folder);
					foreach($files as $k =>$v){
						if(!in_array($v, array('.','..'))){
							unlink($folder."/".$v);
						}
					}
				}else{
					mkdir($folder);
				}
				if(isset($img)){
				for($i = 0 ; $i< count($img);$i++){
						$img[$i]= str_replace('data:image/jpeg;base64,', '', $img[$i] );
						$img[$i] = base64_decode($img[$i]);
						$fname = $id."_".strtotime(date('Y-m-d H:i'))."_".$imgName[$i];
						$upload = file_put_contents($folder."/".$fname,$img[$i]);
					}
				}
			}
		}else{
			$save = $this->db->query("UPDATE venue set ".$data." where id=".$id);
			if($save){
				$folder = "assets/uploads/venue_".$id;
				if(is_dir($folder)){
					$files = scandir($folder);
					foreach($files as $k =>$v){
						if(!in_array($v, array('.','..'))){
							unlink($folder."/".$v);
						}
					}
				}else{
					mkdir($folder);
				}

				if(isset($img)){
				for($i = 0 ; $i< count($img);$i++){
						$img[$i]= str_replace('data:image/jpeg;base64,', '', $img[$i] );
						$img[$i] = base64_decode($img[$i]);
						$fname = $id."_".strtotime(date('Y-m-d H:i'))."_".$imgName[$i];
						$upload = file_put_contents($folder."/".$fname,$img[$i]);
					}
				}
			}
		}
		if($save)
			return 1;
	}
	function delete_venue(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM venue where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_book(){
		extract($_POST);
		$data = " venue_id = '$venue_id' ";
		$data .= ", name = '$name' ";
		$data .= ", address = '$address' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", datetime = '$schedule' ";
		$data .= ", duration = '$duration' ";
		if(isset($status))
		$data .= ", status = '$status' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO venue_booking set ".$data);
		}else{
			$save = $this->db->query("UPDATE venue_booking set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_book(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM venue_booking where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_register(){
		extract($_POST);
		$data = " event_id = '$event_id' ";
		$data .= ", name = '$name' ";
		$data .= ", address = '$address' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		if(isset($status))
		$data .= ", status = '$status' ";
		if(isset($payment_status))
		$data .= ", payment_status = '$payment_status' ";
		else
		$data .= ", payment_status = '0' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO audience set ".$data);
		}else{
			$save = $this->db->query("UPDATE audience set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_register(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM audience where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_event(){
		extract($_POST);
		$data = " event = '$event' ";
		$data .= ",venue_id = '$venue_id' ";
		$data .= ", schedule = '$schedule' ";
		$data .= ", audience_capacity = '$audience_capacity' ";
		if(isset($payment_status))
		$data .= ", payment_type = '$payment_status' ";
		else
		$data .= ", payment_type = '2' ";
		if(isset($type))
			$data .= ", type = '$type' ";
		else
		$data .= ", type = '1' ";
			$data .= ", amount = '$amount' ";
		$data .= ", description = '".htmlentities(str_replace("'","&#x2019;",$description))."' ";
		if($_FILES['banner']['tmp_name'] != ''){
						$_FILES['banner']['name'] = str_replace(array("(",")"," "), '', $_FILES['banner']['name']);
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['banner']['name'];
						$move = move_uploaded_file($_FILES['banner']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", banner = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO events set ".$data);
			if($save){
				$id = $this->db->insert_id;
				$folder = "assets/uploads/event_".$id;
				if(is_dir($folder)){
					$files = scandir($folder);
					foreach($files as $k =>$v){
						if(!in_array($v, array('.','..'))){
							unlink($folder."/".$v);
						}
					}
				}else{
					mkdir($folder);
				}
				if(isset($img)){
				for($i = 0 ; $i< count($img);$i++){
						$img[$i]= str_replace('data:image/jpeg;base64,', '', $img[$i] );
						$img[$i] = base64_decode($img[$i]);
						$fname = $id."_".strtotime(date('Y-m-d H:i'))."_".$imgName[$i];
						$upload = file_put_contents($folder."/".$fname,$img[$i]);
					}
				}
			}
		}else{
			$save = $this->db->query("UPDATE events set ".$data." where id=".$id);
			if($save){
				$folder = "assets/uploads/event_".$id;
				if(is_dir($folder)){
					$files = scandir($folder);
					foreach($files as $k =>$v){
						if(!in_array($v, array('.','..'))){
							unlink($folder."/".$v);
						}
					}
				}else{
					mkdir($folder);
				}

				if(isset($img)){
				for($i = 0 ; $i< count($img);$i++){
						$img[$i]= str_replace('data:image/jpeg;base64,', '', $img[$i] );
						$img[$i] = base64_decode($img[$i]);
						$fname = $id."_".strtotime(date('Y-m-d H:i'))."_".$imgName[$i];
						$upload = file_put_contents($folder."/".$fname,$img[$i]);
					}
				}
			}
		}
		if($save)
			return 1;
	}
	function delete_event(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM events where id = ".$id);
		if($delete){
			return 1;
		}
	}
	
	function get_audience_report(){
		extract($_POST);
		$data = array();
		$event = $this->db->query("SELECT e.*,v.venue FROM events e inner join venue v on v.id = e.venue_id  where e.id = $event_id")->fetch_array();
		foreach($event as $k=>$v){
			if(!is_numeric($k))
			$data['event'][$k]=$v;
		}
		$audience = $this->db->query("SELECT * FROM audience where status = 1 and event_id = $event_id");
		if($audience->num_rows > 0):
			while($row=$audience->fetch_assoc()){
				$row['pstatus'] = $data['event']['payment_type'] == 1 ? "N/A" : ($row['status'] == 1 ? "Paid":'Unpaid');
				$data['data'][]=$row;
			}
		endif;
		return json_encode($data);

	}
	function get_venue_report(){
		extract($_POST);
		$data = array();
		$date = $month.'-01';
		$venue = $this->db->query("SELECT * FROM venue where id = $venue_id")->fetch_array();
		foreach($venue as $k=>$v){
			if(!is_numeric($k))
			$data['venue'][$k]=$v;
		}
		$data['venue']['month']=date("F, d",strtotime($date));
		// echo "SELECT * FROM event where date_format(schedule,'%Y-%m') = '$month' and venue = $venue_id";
		$event = $this->db->query("SELECT * FROM events where date_format(schedule,'%Y-%m') = '$month' and id = $venue_id");
		if($event->num_rows > 0):
			while($row=$event->fetch_assoc()){
				$row['fee'] = $row['payment_type'] == 1 ? "FREE" : number_format($row['amount'],2);
				$row['etype'] = $row['type'] == 1 ? "Public" : "Private";
				$row['sched'] = date("M d,Y h:i A",strtotime($row['schedule']));
				$data['data'][]=$row;
			}
		endif;
		return json_encode($data);

	}
	function save_art_fs(){
		extract($_POST);
		$data = " art_id = '$art_id' ";
		$data .= ", price = '$price' ";
		if(isset($status)){
		$data .= ", status = '$status' ";
		}
		

		if(empty($id)){
			$save = $this->db->query("INSERT INTO arts_fs set ".$data);
			
		}else{
			$save = $this->db->query("UPDATE arts_fs set ".$data." where id=".$id);
		}
		if($save){

			return json_encode(array("status"=>1,"id"=>$id));
		}
	}
	function delete_art_fs(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM arts_fs where id = ".$id);
		if($delete){
				return 1;
			}
	}
	function delete_order(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM orders where id = ".$id);
		if($delete){
				return 1;
			}
	}
	function update_order(){
		extract($_POST);
		$order = $this->db->query("UPDATE orders set status = $status, deliver_schedule = '$deliver_schedule' where id= $order_id ");
		if($order_id){
			if(in_array($status,array(1,3))){
				$fs = $this->db->query("UPDATE arts_fs set status = 1 where id = $fs_id ");
			}else{
				$fs = $this->db->query("UPDATE arts_fs set status = 0 where id = $fs_id ");
			}
			if($fs)
			return 1;
		}
	}
	
	// New functions for enhanced features
	function register_user(){
		extract($_POST);
		$data = " fullname = '$fullname' ";
		$data .= ", email = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", phone = '$phone' ";
		$data .= ", address = '$address' ";
		
		$chk = $this->db->query("SELECT * FROM registered_users where email = '$email'")->num_rows;
		if($chk > 0){
			return 2; // Email already exists
		}
		
		$save = $this->db->query("INSERT INTO registered_users set ".$data);
		if($save){
			return 1;
		}
	}
	
	function user_login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM registered_users where email = '".$email."' and password = '".md5($password)."' and status = 1");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if(!is_numeric($key))
					$_SESSION['user_'.$key] = $value;
			}
			return 1;
		}else{
			return 2; // Invalid credentials
		}
	}
	
	function user_logout(){
		foreach ($_SESSION as $key => $value) {
			if(strpos($key, 'user_') === 0)
				unset($_SESSION[$key]);
		}
		return 1;
	}
	
	function book_event(){
		extract($_POST);
		$data = " user_id = '".$_SESSION['user_id']."' ";
		$data .= ", event_id = '$event_id' ";
		$data .= ", notes = '$notes' ";
		
		// Check if already booked
		$chk = $this->db->query("SELECT * FROM event_bookings where user_id = '".$_SESSION['user_id']."' and event_id = '$event_id'")->num_rows;
		if($chk > 0){
			return 2; // Already booked
		}
		
		$save = $this->db->query("INSERT INTO event_bookings set ".$data);
		if($save){
			return 1;
		}
	}
	
	function cancel_booking(){
		extract($_POST);
		$delete = $this->db->query("UPDATE event_bookings set status = 2 where id = ".$id);
		if($delete){
			return 1;
		}
	}
	
	function get_dashboard_stats(){
		$stats = array();
		
		// Total events
		$total_events = $this->db->query("SELECT COUNT(*) as count FROM events")->fetch_assoc();
		$stats['total_events'] = $total_events['count'];
		
		// Upcoming events
		$upcoming = $this->db->query("SELECT COUNT(*) as count FROM events where event_status = 0")->fetch_assoc();
		$stats['upcoming_events'] = $upcoming['count'];
		
		// Ongoing events
		$ongoing = $this->db->query("SELECT COUNT(*) as count FROM events where event_status = 1")->fetch_assoc();
		$stats['ongoing_events'] = $ongoing['count'];
		
		// Completed events
		$completed = $this->db->query("SELECT COUNT(*) as count FROM events where event_status = 2")->fetch_assoc();
		$stats['completed_events'] = $completed['count'];
		
		// Total venues
		$total_venues = $this->db->query("SELECT COUNT(*) as count FROM venue")->fetch_assoc();
		$stats['total_venues'] = $total_venues['count'];
		
		// Total registered users
		$total_users = $this->db->query("SELECT COUNT(*) as count FROM registered_users")->fetch_assoc();
		$stats['total_users'] = $total_users['count'];
		
		// Total bookings
		$total_bookings = $this->db->query("SELECT COUNT(*) as count FROM event_bookings")->fetch_assoc();
		$stats['total_bookings'] = $total_bookings['count'];
		
		// Pending bookings
		$pending = $this->db->query("SELECT COUNT(*) as count FROM event_bookings where status = 0")->fetch_assoc();
		$stats['pending_bookings'] = $pending['count'];
		
		return json_encode($stats);
	}
	
	function search_events(){
		extract($_POST);
		$search = "%$search%";
		$events = $this->db->query("SELECT e.*, v.venue FROM events e 
			LEFT JOIN venue v ON e.venue_id = v.id 
			WHERE e.event LIKE '$search' OR v.venue LIKE '$search' OR e.description LIKE '$search'
			ORDER BY e.schedule DESC");
		
		$data = array();
		while($row = $events->fetch_assoc()){
			$data[] = $row;
		}
		return json_encode($data);
	}
	
	function update_event_status(){
		extract($_POST);
		$save = $this->db->query("UPDATE events set event_status = '$status' where id = ".$id);
		if($save){
			return 1;
		}
	}
	
	function get_my_bookings(){
		$user_id = $_SESSION['user_id'];
		$bookings = $this->db->query("SELECT eb.*, e.event, e.schedule, v.venue 
			FROM event_bookings eb 
			LEFT JOIN events e ON eb.event_id = e.id 
			LEFT JOIN venue v ON e.venue_id = v.id 
			WHERE eb.user_id = $user_id 
			ORDER BY eb.booking_date DESC");
		
		$data = array();
		while($row = $bookings->fetch_assoc()){
			$data[] = $row;
		}
		return json_encode($data);
	}
	
	function check_booking_exists(){
		extract($_POST);
		$chk = $this->db->query("SELECT * FROM event_bookings where user_id = '".$_SESSION['user_id']."' and event_id = '$event_id'")->num_rows;
		return $chk;
	}
}
