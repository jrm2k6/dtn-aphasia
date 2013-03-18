<?php

include_once("database.php");
$value_cb_checked = "";
$cb_checked = $_POST['cb_iscorrect'];
$pdo=db_connect('mysql:host=localhost;dbname=dtn_aphasia');
$drop_table_current_session_query = 'TRUNCATE TABLE current_session';
$results_drop_table_current_session_query = $pdo->query($drop_table_current_session_query) ;
$folder_sender = '../sender';
foreach($cb_checked as $key=>$value){
	$value_cb_checked = $value_cb_checked.";".$value;
	
}


$results_to_check = explode(";",$value_cb_checked);

foreach($results_to_check as $results){
	//echo $results.'<br \>';
	$results_split = explode("_",$results);
	//echo $results_split.'<br \>';
	$tab = array();
	foreach($results_split as $words){
		$tab[]=$words;
	}
	

	$array_id = explode('/',$tab[0]);
	$id_phone_current_patient = $array_id[1];
	$name_patient = $tab[1].'_'.$tab[2];
	$query_level = 'SELECT tbl_historypatient_level, tbl_historypatient_nb_exercises_completed FROM history_patient WHERE tbl_historypatient_patient_name_patient=\''.$name_patient.'\'';
	$results_select_level = $pdo->query($query_level) ;
		while ($row = $results_select_level->fetch ())
	   	{
			$patient_level = $row[0];
			$nb_exercises_already_done = $row[1];
			//echo $patient_level.'<br \>';
			//echo $nb_exercises_already_done.'<br \>';
			$query_nb_exercise_for_current_level = 'SELECT tbl_level_nb_exercise_to_validate FROM level WHERE tbl_level_value_level = \''.$patient_level.'\'';
			$results_select_nb_exercise = $pdo->query($query_nb_exercise_for_current_level) ;
			while ($row = $results_select_nb_exercise->fetch ())
			{
				if($nb_exercises_already_done+1 == $row[0])
				{
					// We have to increase the level of the patient, and notify the therapist
					echo '<div id="level_notification">Level of patient '.$name_patient.' has been increased</div>';
					$new_level = $patient_level+1;
					$update_nb_exercise_done = 'UPDATE history_patient SET tbl_historypatient_level='.$new_level.',
					 tbl_historypatient_nb_exercises_completed =0,tbl_historypatient_date_level=CURDATE() WHERE tbl_historypatient_patient_name_patient=\''.$name_patient.'\'';
					$results_update_nb_exercise = $pdo->exec($update_nb_exercise_done) ;
					// Creation of the sender folder if not existing
					if (!is_dir($folder_sender)){ mkdir($folder_sender); }
					// Creation of a file which will contains the new level of the current patient
					$fh = fopen($folder_sender.'/'.$id_phone_current_patient.'_'.$name_patient.'_level','w') or die("can't open file");
					fwrite($fh,''.$new_level);
					fclose($fh);
					
				} elseif($nb_exercises_already_done+1 < $row[0]) {
					//echo 'Increase level not allowed';
					$nb_exercise_updated = $nb_exercises_already_done+1;
					$update_nb_exercise_done = 'UPDATE history_patient SET tbl_historypatient_nb_exercises_completed='.$nb_exercise_updated.' WHERE 						tbl_historypatient_patient_name_patient=\''.$name_patient.'\'';
					$results_update_nb_exercise = $pdo->exec($update_nb_exercise_done) ;
				}
			}
		}
	
sleep(3);//seconds to wait..
header("Location:../index.php");
}

















?>
