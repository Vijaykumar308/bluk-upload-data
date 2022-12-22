<?php
    include "functions.php";
    if($_COOKIE['auth-username'] != "admin"){
        die("Your don't have permission !");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <link rel="shortcut icon" sizes="196x196" href="../../../../client/img/favicon.ico">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <title>Upload csv</title>
    <style>
        .header{
            display: flex;
            /* justify-content: space-around; */
        }  
        .uploadcsv{
            display: flex;
            width: 100%;
            padding: 0;
            margin-bottom: 20px;
            font-size: 21px;
            line-height: 50px;
            color: #333;
            border: 0;
            justify-content: space-between;
            border-bottom: 1px solid #e5e5e5;
        }  
        .form-designing{
            margin-top: 6%;
            /* box-shadow: 2px 1px 6px -2px black; */
            box-shadow: 0px 0px 6px -2px black;
            padding: 47px 0px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div id="wrap">
        <div class="container">
            <div class="row">
                <form class="form-horizontal" action="functions.php" method="post" name="upload_excel" enctype="multipart/form-data">
                    <fieldset>
                        <!-- Form Name -->
                        <div class="uploadcsv">
                            <div> UPLOAD CSV</div>
                            <div class>
                                <a href="functions.php?download=sample">
                                    <button type="button" class="btn btn-success">Dowload Sample</button>
                                </a>
                            </div>
                        </div>
                        <!-- File Button -->
                        <div class="form-group form-designing">

                            <label class="col-md-2 control-label" for="filebutton">Select Campaign<span style="color: red;font-size:16px;">*</span></label>
                            <div class="col-md-2">
                                <select name="campaign" id="campaign" class="input-date" style="padding: 3px 1px; width:110%">
                                    <option value="--Select--">--Select--</option>
                                    <option value="Happy Calling">Happy Calling</option>
                                    <option value="samsungIB">samsungIB</option>
                                    <option value="CUG">CUG</option>
                                    <option value="OOW">OOW</option>
                                    <option value="Cart Drop">Cart Drop</option>
                                    <option value="Payment Failure/Cancellation">Payment Failure/Cancellation</option>
                                    <option value="BYOD">BYOD</option>
                                    <option value="Flagship">Flagship</option>
                                    <option value="Offline">Offline</option>
                                    <option value="Click to Call">Click to Call</option>
                                </select>
                            </div>

            <label class="col-md-2 control-label" for="filebutton">Select File<span style="color: red;">*</span></label>
                            <div class="col-md-3">
                                <input type="file" name="file" id="file" class="input-large">
                            </div>

                            <div class="col-md-3">
                                <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">Import</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <?php
              // get_all_records();
            ?>
        </div>
    </div>
</body>
</html>
