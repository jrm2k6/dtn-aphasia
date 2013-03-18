<?php
# pdo_db_connect - function for connecting to the database. If the database is not existing we create thed database

function db_connect($dsn){
	$con = mysql_connect("localhost","root","root");
	if (mysql_query("CREATE DATABASE dtn_aphasia DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",$con))
	mysql_close($con);
	$pdo = new PDO( $dsn,'root', 'root', 
    	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
	);
	return($pdo);


}

 
function create_database_and_tables($dsn, $pdo){
	
	
	
	$pdo->exec("CREATE TABLE IF NOT EXISTS patient (tbl_patient_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,tbl_patient_name_patient VARCHAR(60) NOT NULL,tbl_patient_id_phone VARCHAR(20) NOT NULL);");
	$pdo->exec("CREATE TABLE IF NOT EXISTS level (tbl_level_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,tbl_level_value_level INTEGER NOT NULL,tbl_level_nb_exercise_to_validate INTEGER NOT NULL);");
	$pdo->exec("CREATE TABLE IF NOT EXISTS history_patient (tbl_historypatient_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,tbl_historypatient_patient_name_patient VARCHAR(60) NOT 			  	NULL,tbl_historypatient_level INTEGER NOT NULL, tbl_historypatient_nb_exercises_completed INTEGER NOT NULL, tbl_historypatient_date_level DATE NOT NULL);");
	$pdo->exec("CREATE TABLE IF NOT EXISTS current_session (tbl_currentsession_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,tbl_currentsession_name_patient VARCHAR(60) NOT 			 				NULL,tbl_currentsession_name_exercise VARCHAR(100) NOT NULL);");
}


function insert_data($pdo){
	$pdo->exec("INSERT INTO level(tbl_level_value_level,tbl_level_nb_exercise_to_validate) VALUES (1,2)");
	$pdo->exec("INSERT INTO level(tbl_level_value_level,tbl_level_nb_exercise_to_validate) VALUES (2,4)");
	$pdo->exec("INSERT INTO level(tbl_level_value_level,tbl_level_nb_exercise_to_validate) VALUES (3,6)");
}


function data_already_existing($pdo){
	$result_query_level=$pdo->query("SELECT * from level");
	if($result_query_level ->rowCount() > 0)
	{
		return true;
	} else {
		return false;
	}
}

?>
