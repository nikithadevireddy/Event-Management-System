<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_venue"){
	$save = $crud->save_venue();
	if($save)
		echo $save;
}
if($action == "save_book"){
	$save = $crud->save_book();
	if($save)
		echo $save;
}
if($action == "delete_book"){
	$save = $crud->delete_book();
	if($save)
		echo $save;
}

if($action == "save_register"){
	$save = $crud->save_register();
	if($save)
		echo $save;
}
if($action == "delete_register"){
	$save = $crud->delete_register();
	if($save)
		echo $save;
}
if($action == "delete_venue"){
	$save = $crud->delete_venue();
	if($save)
		echo $save;
}
if($action == "update_order"){
	$save = $crud->update_order();
	if($save)
		echo $save;
}
if($action == "delete_order"){
	$save = $crud->delete_order();
	if($save)
		echo $save;
}
if($action == "save_event"){
	$save = $crud->save_event();
	if($save)
		echo $save;
}
if($action == "delete_event"){
	$save = $crud->delete_event();
	if($save)
		echo $save;
}
if($action == "save_artist"){
	$save = $crud->save_artist();
	if($save)
		echo $save;
}
if($action == "delete_artist"){
	$save = $crud->delete_artist();
	if($save)
		echo $save;
}
if($action == "get_audience_report"){
	$get = $crud->get_audience_report();
	if($get)
		echo $get;
}
if($action == "get_venue_report"){
	$get = $crud->get_venue_report();
	if($get)
		echo $get;
}
if($action == "save_art_fs"){
	$save = $crud->save_art_fs();
	if($save)
		echo $save;
}
if($action == "delete_art_fs"){
	$save = $crud->delete_art_fs();
	if($save)
		echo $save;
}
if($action == "get_pdetails"){
	$get = $crud->get_pdetails();
	if($get)
		echo $get;
}
if($action == 'register_user'){
	$save = $crud->register_user();
	if($save)
		echo $save;
}
if($action == 'user_login'){
	$login = $crud->user_login();
	if($login)
		echo $login;
}
if($action == 'user_logout'){
	$logout = $crud->user_logout();
	if($logout)
		echo $logout;
}
if($action == 'book_event'){
	$save = $crud->book_event();
	if($save)
		echo $save;
}
if($action == 'cancel_booking'){
	$save = $crud->cancel_booking();
	if($save)
		echo $save;
}
if($action == 'get_dashboard_stats'){
	$get = $crud->get_dashboard_stats();
	if($get)
		echo $get;
}
if($action == 'search_events'){
	$get = $crud->search_events();
	if($get)
		echo $get;
}
if($action == 'update_event_status'){
	$save = $crud->update_event_status();
	if($save)
		echo $save;
}
if($action == 'get_my_bookings'){
	$get = $crud->get_my_bookings();
	if($get)
		echo $get;
}
if($action == 'check_booking_exists'){
	$get = $crud->check_booking_exists();
	if($get)
		echo $get;
}
