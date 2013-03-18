<?php 



function updateDatabaseWithNewPatients($path){
	$d = dir($path);
	$pdo=db_connect('mysql:host=localhost;dbname=dtn_aphasia');
	while($entry = $d->read()) {
		if(strcmp($entry,".")&&strcmp($entry,"..")){
			$file_split = explode("_",$entry);
			$name = $file_split[1].'_'.$file_split[2];
			$id_phone = $file_split[0];
			$name_exercise = $file_split[3];
			$query_select_patient= 'SELECT tbl_patient_id_phone FROM patient WHERE tbl_patient_name_patient=\''.$name.'\'';
			$query_insert_patient = 'INSERT INTO patient(tbl_patient_name_patient,tbl_patient_id_phone) VALUES (\''.$name.'\',\''.$id_phone.'\');';
			$query_insert_history_patient = 'INSERT INTO history_patient(tbl_historypatient_patient_name_patient,tbl_historypatient_level,tbl_historypatient_nb_exercises_completed, 				tbl_historypatient_date_level) VALUES (\''.$name.'\',1,0,CURDATE());';
			$results_select = $pdo->query($query_select_patient) ;
			
			

			if($results_select->rowCount() == 0){
				$results_insert_patient = $pdo->exec($query_insert_patient);
				$results_insert_history_patient = $pdo->exec($query_insert_history_patient);
			} 
		
			if(!preg_match("/\.ogv/",$name_exercise)){
				$query_insert_exo = 'INSERT INTO current_session(tbl_currentsession_name_patient,tbl_currentsession_name_exercise) VALUES (\''.$name.'\',\''.$name_exercise.'\');';
				$results_insert_patient = $pdo->exec($query_insert_exo);
			}			
		}
	}
	$d->close();
	
  }


function printResultsForPatient($path){
	$patient_array = array();
	$pdo=db_connect('mysql:host=localhost;dbname=dtn_aphasia');
	$query_select_patient= 'SELECT tbl_patient_name_patient FROM patient';
	$results_select = $pdo->query($query_select_patient) ;
	while ($row = $results_select->fetch ())
   	{
		$patient_array[] =  $row[0];
	}
	echo '<form id="form_verification" action="php/verification.php" method="post" target="_self">';
	foreach($patient_array as $patient){
		echo '<div id=\''.$patient.'\' >';
		echo '<div id="titre_patient" style="text-transform:uppercase; color: #A7C942" >PATIENT - '.$patient.'</div><br \>';
		
		$query_exercise = 'SELECT tbl_currentsession_name_exercise FROM current_session WHERE tbl_currentsession_name_patient=\''.$patient.'\'';
		$query_id_phone = 'SELECT tbl_patient_id_phone FROM patient WHERE tbl_patient_name_patient=\''.$patient.'\'';
		$results_select_exercise = $pdo->query($query_exercise) ;
		$results_select_id_phone = $pdo->query($query_id_phone);
		if($results_select_id_phone->rowCount() == 1){
				$res = $results_select_id_phone->fetch();
				$current_id_phone = $res[0];
			} 
		
		if($results_select_exercise->rowCount() > 0){
			
			while ($row = $results_select_exercise->fetch ())
	   		{
				$name_file_to_listen = $path.$current_id_phone.'_'.$patient.'_'.$row[0];
				if(!file_exists($name_file_to_listen.'ogv')){
					convertInOggFile($name_file_to_listen);
					unlink($name_file_to_listen);
				}
				echo '<div id="frame" style="border : 1px solid #D4D4D4; background-color : #E5EECC; margin-left : 20px;">'; 
				echo '<div id="name_exercise" style="color : #617F10;" >EXERCISE - '.$row[0].'</div>';
				echo '<div id="audiodiv" style="padding-top : 10px;">';
				echo '<audio controls="controls">';
	  			echo '<source src="'.$name_file_to_listen.'.ogv" type="audio/ogg" />';
	  			echo 'Your browser does not support the audio element.';
				echo '</audio>';
				echo '<br \>';
				
				echo '</div>';
				echo '<input type="checkbox" name="cb_iscorrect[]"  value="'.$name_file_to_listen.'" /> Record is correct<br />';
				echo '</div>';
			}
			
			echo '</div>';	
		}
	
	}
	echo '<div class="buttonHolder" style="text-align: center; ">';
	echo '<input type="submit" value="Validate" style="vertical-align : bottom;" />';
	echo '</div>';
	echo '</form>';
}



function convertInOggFile($file){
	// Calling to the ffmpeg2theora
	echo exec('./ffmep.sh '.$file.' 2>&1', $output);
	
}




function createSenderFolder($directory){
	
	if(!is_dir($directory)){
		mkdir($directory);
	}else { 
		deleteAll($directory,true); 
		}
	
}



function deleteAll($directory, $empty = false) {
    if(substr($directory,-1) == "/") {
        $directory = substr($directory,0,-1);
    }

    if(!file_exists($directory) || !is_dir($directory)) {
        return false;
    } elseif(!is_readable($directory)) {
        return false;
    } else {
        $directoryHandle = opendir($directory);
       
        while ($contents = readdir($directoryHandle)) {
            if($contents != '.' && $contents != '..') {
                $path = $directory . "/" . $contents;
               
                if(is_dir($path)) {
                    deleteAll($path);
                } else {
                    unlink($path);
                }
            }
        }
       
        closedir($directoryHandle);

        if($empty == false) {
            if(!rmdir($directory)) {
                return false;
            }
        }
       
        return true;
    }
} 


?>
