<?php
// load the db connection files
include 'dbconn.php';
ini_set('max_execution_time', 0); // its has set to zero because it will take complete time to get upload data
function pre($arr)
{
  echo "<pre>";
  print_r($arr);
  echo "</pre>";
}

$con = getdb();

if (isset($_POST["Import"])) {
  $filename = $_FILES["file"]["tmp_name"];
  $fileOrignalName = $_FILES['file']['name'];

  $extension = pathinfo($fileOrignalName, PATHINFO_EXTENSION);

  $campaign = $_POST['campaign']; 

// validation related UI;
  if($campaign === "--Select--"){   
    echo "<script type=\"text/javascript\">
    alert(\"Please select a campaign !\");
    window.location = \"index.php\"
    </script>";
    exit; // don't remove exit, this statement is imp here.
  }

  if($fileOrignalName == ""){
    echo "<script type=\"text/javascript\">
    alert(\"Please Upload CSV File.\");
    window.location = \"index.php\"
    </script>"; 
    exit;
  }

  if ($extension != "csv") {
    echo "<script type=\"text/javascript\">
    alert(\"Invalid File:Please Upload CSV File.\");
    window.location = \"index.php\"
    </script>";
    exit; // don't remove exit, this statement is imp here. 
  }

  function mobileno_validate($mobileNo)
  {
    if (
      (!empty($mobileNo)) &&
      is_numeric($mobileNo)  &&
      (strlen($mobileNo) === 10)
    ) {
      return true;
    } else {
      return false;
    }
  }
  function header_validate($header){
    $validHeader = array("mobile_no","honda_city","auto_mobiles","technology","cars");
    if($validHeader === $header){
      return 1;
    }else{
      return 0;
    }
   }
  $invalidData = array(); 
  //inside if checking; file has data or not 
  if ($_FILES["file"]["size"] > 2) {
    $file = fopen($filename, "r");
    
    $rownumberinCSV = 0; 
    $checkHeader = true;
    while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
      $id = uniqid() . date("Y"); 

      if($checkHeader){
        $valid = header_validate($getData);
       // echo "header_validate status: $valid";
        if(!$valid){
          echo "<script type=\"text/javascript\">
          alert(\"Please Provide the correct CSV header !\");
          window.location = \"index.php\"
          </script>";
          exit;
        }
          $checkHeader = false;
      }

      $created_at = date('Y-m-d H:i:s');
      $modified_at = date("Y-m-d H:i:s");

      if (mobileno_validate($getData[0]) && ($getData[0] != 'mobile_no')) {   

        $sql = "SELECT * FROM table1 where mobile_no = " . $getData[0];
        $emp_exist = mysqli_query($con, $sql);

        if ($emp_exist->num_rows > 0) {
          
          $update = "Update query, if mobile no is duplicate";

          $result = mysqli_query($con, $update);

        } else {    
         // $sql = "Insert query to insert data";
          $result = mysqli_query($con, $sql); 
        }
        $rownumberinCSV++;
      } else {
        $rownumberinCSV++;
        array_push($getData,$rownumberinCSV);
        
        if (empty($getData[0])) 
          array_push($getData, "mobile number not given");

        else if (!is_numeric($getData[0])) 
          array_push($getData, "mobile number must of number");
          
        else if (strlen($getData[0]) != 10) 
          array_push($getData, "mobile number should be of 10 digit");
        else{
        }
        array_splice($getData, 0,4);
        array_push($invalidData, $getData);
      }
    } //while end;
    fclose($file);
    
    array_shift($invalidData);
    if(!empty($invalidData)){
      invalid_csv_data($invalidData);
    }else{
      //  echo "Data inserted successfully";
      echo "<script type=\"text/javascript\">
          alert(\"Data inserted succesfully.\");
          window.location = \"index.php\"
      </script>";
    }
  }else{
    echo "<script type=\"text/javascript\">
    alert(\"You're file is empty!\");
    window.location = \"index.php\"
    </script>";
  }
}

//Download Sample sheet 
if (isset($_GET['download'])) {
  //  echo "download sample"; 
  $delimiter = ",";
  $filename = "Samplefile.csv";

  // Create a file pointer 
  $f = fopen("php://memory", "w");

  $csvheaders = array("mobile_no","honda_city","auto_mobiles","technology","cars");
  fputcsv($f, $csvheaders, $delimiter);

  // Move back to beginning of file 
  fseek($f, 0);

  //output all remaining data on a file pointer 
  fpassthru($f);
  // Set headers to download file rather than displayed 
  header('Content-Disposition: attachment; filename="' . $filename . '";');
  header('Content-type: application/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
}

function invalid_csv_data($array)
{
  $filename = "invalid_Data.csv";
  // open raw memory as file so no temp files needed, you might run out of memory though
  $f = fopen('php://memory', 'w');
  $delimiter = ",";
  // loop over the input array
  $headers = array("rowNumber", "reason");
  fputcsv($f, $headers, $delimiter);

  foreach ($array as $line) {
    fputcsv($f, $line, $delimiter);
  }
  // reset the file pointer to the start of the file
  fseek($f, 0);

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '";');
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  fpassthru($f);
  fclose($f);
}

function get_all_records()
{
  $con = getdb();
  $Sql = "select * from table2 WHERE campaign = '$campaign'";
  $result = mysqli_query($con, $Sql);


  if (mysqli_num_rows($result) > 0) {
    echo "<div class='table-responsive'><table id='myTable' class='table table-striped table-bordered'>
          <thead><tr><th>Auto Mobiles</th>
            <th>Cars</th>
            <th>Technology</th>
            <th>Registration Date</th>
          </tr></thead><tbody>";


    while ($row = mysqli_fetch_assoc($result)) {

      echo "<tr><td>" . $row['mobile_nos'] . "</td>
                       <td>" . $row['auto_mobile'] . "</td>
                       <td>" . $row['Cars'] . "</td>
                       <td>" . $row['Technology'] . "</td>
                       <td>" . $row['created_at'] . "</td></tr>";
    }

    echo "</tbody></table></div>";
  } else {
    echo "you have no records";
  }
}
