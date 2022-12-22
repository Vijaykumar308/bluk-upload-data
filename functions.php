<?php
include 'connection.php';
ini_set('max_execution_time', 0);
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
    $validHeader = array("mobile_no", "Customer Name", "Product/Device","Store","Amount","SKU/Model code", "Customer email","Pincode", "Link","Coupon Code","Additional Info1","Additional Info2");
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

   /* function utc_to_ist($created_at){

        $time = strtotime($created_at);

        $now = date("Y-m-d H:i:s",$time);
        
        $new_time = date("Y-m-d H:i:s", strtotime('+5 hours 30 minutes', strtotime($now)));
        return $new_time;
    }
*/
    // $created_at = utc_to_ist($created_at);
    // $modified_at = utc_to_ist($modified_at);

      if (mobileno_validate($getData[0]) && ($getData[0] != 'mobile_no')) {   

        $sql = "SELECT * FROM customer where FROM_BASE64(mobile_no) = " . $getData[0];
        $emp_exist = mysqli_query($con, $sql);

        if ($emp_exist->num_rows > 0) {
          
          $update = "UPDATE `customer` SET `id`='$id', `name`='$getData[1]', `device`='$getData[2]',
          `store`='$getData[3]',`amount`='$getData[4]',`sku`='$getData[5]',
          `email`='".base64_encode($getData[6])."',`pincode`='$getData[7]',
          `link`='$getData[8]',`coupon_code`='$getData[9]',`addition_info1`='$getData[10]',`addition_info2`='$getData[11]', 
          `modified_at`='$modified_at',`campaign`='$campaign' WHERE FROM_BASE64(`mobile_no`) = '$getData[0]'";

          $result = mysqli_query($con, $update);

        } else {    
         // $sql = "INSERT into customer (id,mobile_no,name,created_at,campaign,email,handset,offer_pitch,addition_info1,addition_info2) 
          //         values ('". $id ."','" . base64_encode($getData[0]) . "','" . $getData[1] . "','" . $created_at . "','" . $campaign . "','" . base64_encode($getData[2]) . "','" . $getData[3] . "','" . $getData[4] . "','" . $getData[5] . "','$getData[6]')"; 
          $sql = "INSERT INTO customer (id,
          mobile_no, name, device, store, amount, sku, email, pincode, link, coupon_code,
          addition_info1, addition_info2,  created_at, modified_at, campaign) VALUES 
          ('".$id."','" . base64_encode($getData[0])."', '".$getData[1]."','".$getData[2]."',
          '".$getData[3]."','".$getData[4]."','".$getData[5]."','" . base64_encode($getData[6])."','".$getData[7]."',
          '".$getData[8]."','".$getData[9]."','".$getData[10]."','".$getData[11]."', '".$created_at."',
          '".$modified_at."', '".$campaign."')";
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
        array_splice($getData, 0,7);
        array_push($invalidData, $getData);
      }
    } //while end;
    fclose($file);
    
    array_shift($invalidData);
    if(!empty($invalidData)){
      invalid_csv_data($invalidData);
    }else{
        echo "Data inserted successfully";
      // echo "<script type=\"text/javascript\">
      //     alert(\"Data inserted succesfully.\");
      //     window.location = \"index.php\"
      // </script>";
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

  $csvheaders = array("mobile_no", "Customer Name", "Product/Device","Store","Amount","SKU/Model code", "Customer email","Pincode", "Link","Coupon Code","Additional Info1","Additional Info2");
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
  $Sql = "SELECT * , FROM_BASE64(mobile_no) as mobile_nos, FROM_BASE64(email) as emails FROM customer";
  $result = mysqli_query($con, $Sql);


  if (mysqli_num_rows($result) > 0) {
    echo "<div class='table-responsive'><table id='myTable' class='table table-striped table-bordered'>
          <thead><tr><th>EMP ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Registration Date</th>
          </tr></thead><tbody>";


    while ($row = mysqli_fetch_assoc($result)) {

      echo "<tr><td>" . $row['mobile_nos'] . "</td>
                       <td>" . $row['name'] . "</td>
                       <td>" . $row['campaign'] . "</td>
                       <td>" . $row['emails'] . "</td>
                       <td>" . $row['reg_date'] . "</td></tr>";
    }

    echo "</tbody></table></div>";
  } else {
    echo "you have no records";
  }
}
