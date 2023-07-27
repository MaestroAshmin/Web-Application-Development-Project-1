<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShipOnline System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               
                if(isset($_POST['submit'])){
                    if (empty($_POST["retrieve_date"])) {  
                        $preferred_dateErr = "Please select a date to retrieve";  
                    }
                    if (empty($_POST["date_type"])) {  
                        $date_typeErr = "Please select to retrieve whether by Request date or Pick-up Date";  
                    }
                }
                else{
                    echo 'false';
                }
            }
    
        ?>
    <div class="container">
        <h2 class="d-flex justify-content-center">ShipOnline System Administration Page</h2>
        <div class="form-component">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/formdata">
                <div class="form-element">
                    <label for="retrieve_date" class="form-label">Date for retrieve:</label>
                    <input type="date" name="retrieve_date" id="retrieve_date">
                </div>
                <?php if(!empty($preferred_dateErr)){ ?>
                    <span class="error">* <?php echo $preferred_dateErr; ?> </span></br>
                    <?php
                    }
                ?>
                <div class="form-element">
                    <label for="date_type" class="form-label">Select Date Item for retrieve:</label>
                    <input type="radio" name="date_type" id="request_date" value="R">
                    <label for="Request Date">Request Date </label>
                    <input type="radio" name="date_type" id="pickup_date" value="P">
                    <label for="Pick-up Date">Pick-up Date </label>
                        
                </div>
                <?php if(!empty($date_typeErr)){ ?>
                    <span class="error">* <?php echo $date_typeErr; ?> </span></br>
                    <?php
                    }
                ?>
                <button type="submit" name="submit" value ="submit" class="btn btn-primary">Show</button>
                <br />
            </form>
        </div>
     <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "wad-project1";
                if(isset($_POST['submit'])){
                    if (empty($_POST["retrieve_date"])) {  
                        $preferred_dateErr = "Please select a date to retrieve";  
                    }
                    if (empty($_POST["date_type"])) {  
                        $date_typeErr = "Please select to retrieve whether by Request date or Pick-up Date";  
                    }
                    if(empty($preferred_dateErr) && empty($date_typeErr)){
                        $date_type = $_POST['date_type'];
                        $date = $_POST['retrieve_date'];
                        $conn = mysqli_connect($servername, $username, $password, $dbname);
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $date_low = $date." 00:00:00";
                        $date_high = $date." 23:59:59";
                        if($date_type == 'R'){
                            $sql = "SELECT customer_number, request_number, item_description, item_weight, pickup_suburb, pickup_date, delivery_suburb, delivery_state FROM request WHERE request_date >='$date_low' and request_date <='$date_high'";
                            $result =mysqli_query($conn, $sql);
                            if(mysqli_num_rows($result) > 0){
                                $total_revenue= 0;
                                echo '<div class="table-component">
                                        <table class="table table-bordered">
                                            <thead><tr><th>S.N</th>
                                                <th>Customer Number</th>
                                                <th>Request Number</th>
                                                <th>Item Description</th>
                                                <th>Weight</th>
                                                <th>Pick-up Suburb</th>
                                                <th>Preferred Pick-up Date</th>
                                                <th>Delivery Suburb</th>
                                                <th>State</th>
                                                <th>Cost</th>
                                                </tr>
                                            </thead>
                                        <tbody>';
                                        $sn=1;
                                while($data = mysqli_fetch_assoc($result)){
                                    if($data['item_weight'] <=2){
                                        $cost = 10;
                                    }
                                    else{
                                        $cost = 10 + ($data['item_weight']-2)*2;
                                    }
                                    $total_revenue = $total_revenue + $cost;
                                    echo "<tr>";
                                    echo "<td>".$sn."</td>";
                                    echo "<td>".$data['customer_number']."</td>";
                                    echo "<td>".$data['request_number']."</td>";
                                    echo "<td>".$data['item_description']."</td>";
                                    echo "<td>".$data['item_weight']."</td>";
                                    echo "<td>".$data['pickup_suburb']."</td>";
                                    echo "<td>".$data['pickup_date']."</td>";
                                    echo "<td>".$data['delivery_suburb']."</td>";
                                    echo "<td>".$data['delivery_state']."</td>";
                                    echo "<td> $".$cost."</td>";
                                    echo "</tr>";
                                    $sn++;
                                }
                                $total_requests = $sn-1;
                                echo '</tbody>
                                </table>
                                </div>';
                                echo '<div class="total">
                                        <h3>Total Number of Requests on '.$date.' is '.$total_requests.'</h3>
                                        <h3>Total Revenue on '.$date.' is $'.$total_revenue.'</h3>
                                    </div>
                                ';
                            }
                        
                            else {
                                echo '<h3 class="d-flex justify-content-center">
                                    No Result found for date '.$date.'
                                </h3>';
                            }
                        }
                        else{
                            $sql = "SELECT c.customer_number, c.customer_name, c.contact_phone_number, r.request_number, r.item_description, r.item_weight, r.pickup_address, r.pickup_suburb, r.pickup_date, r.delivery_suburb, r.delivery_state FROM request as r INNER JOIN customer as c ON r.customer_number=c.customer_number WHERE r.pickup_date = '$date'";
                            $result =mysqli_query($conn, $sql);
                            if(mysqli_num_rows($result) > 0){
                                $total_weight= 0;
                                echo '<div class="table-component">
                                        <table class="table table-bordered">
                                            <thead><tr><th>S.N</th>
                                                <th>Customer Number</th>
                                                <th>Customer Name</th>
                                                <th>Contact Phone</th>
                                                <th>Request Number</th>
                                                <th>Item Description</th>
                                                <th>Weight</th>
                                                <th>Pick-up Suburb</th>
                                                <th>Preferred Pick-up Date</th>
                                                <th>Delivery Suburb</th>
                                                <th>State</th>
                                                </tr>
                                            </thead>
                                        <tbody>';
                                        $sn=1;
                                while($data = mysqli_fetch_assoc($result)){
                                    $total_weight += $data['item_weight'];
                                    echo "<tr>";
                                    echo "<td>".$sn."</td>";
                                    echo "<td>".$data['customer_number']."</td>";
                                    echo "<td>".$data['customer_name']."</td>";
                                    echo "<td>".$data['contact_phone_number']."</td>";
                                    echo "<td>".$data['request_number']."</td>";
                                    echo "<td>".$data['item_description']."</td>";
                                    echo "<td>".$data['item_weight']."</td>";
                                    echo "<td>".$data['pickup_suburb']."</td>";
                                    echo "<td>".$data['pickup_date']."</td>";
                                    echo "<td>".$data['delivery_suburb']."</td>";
                                    echo "<td>".$data['delivery_state']."</td>";
                                    echo "</tr>";
                                    $sn++;
                                }
                                $total_requests = $sn-1;
                                echo '</tbody>
                                </table>
                                </div>';
                                echo '<div class="total">
                                        <h3>Total Number of Requests on '.$date.' is '.$total_requests.'</h3>
                                        <h3>Total Weight of all requests '.$date.' is '.$total_weight.' kg</h3>
                                    </div>
                                ';
                            }
                        
                            else {
                                // echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                echo '<h3 class="d-flex justify-content-center">
                                    No Result found for date '.$date.'
                                </h3>';
                            }
                        }
                        
                        mysqli_close($conn);
                    }
                }
                else{
                    echo 'false';
                }
            }
    
        ?>       
        <a href="shiponline.php" class="d-flex justify-content-center">Home</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>