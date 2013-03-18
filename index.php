<html>
<head><title>DTN Aphasia Manager</title>
<link rel="stylesheet" type="text/css" href="style.css" /> 
<script language="javascript" src="javascript/function.js"></script>
</head>
<body>

<?php
require_once("php/database.php");
require_once("php/utils.php");
include_once('php/Patient.class.php');
$pdo=db_connect('mysql:host=localhost;dbname=dtn_aphasia');
create_database_and_tables('mysql:host=localhost;dbname=dtn_aphasia',$pdo);
if(!data_already_existing($pdo)){
	insert_data($pdo);
	}


$directory = 'sender';
createSenderFolder($directory)
?>
<div id="mainDiv">


<?php updateDatabaseWithNewPatients("audio/"); 
 printResultsForPatient("audio/");


?>

</div>

</body>
</html>
