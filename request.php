<?php
session_start();
    if(!isset($_SESSION['customer_number'])){
        header('Location: login.php');
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['submit'])){
                if (empty($_POST["description"])) {  
                    $descriptionErr = "Item Description is required";  
                }
                if (empty($_POST["weight"])) {  
                    $weightErr = "Please select Weight";  
                }
                if (empty($_POST["pickup_address"])) {  
                    $pickup_addressErr = "Please enter an address for pickup";  
                }
                if (empty($_POST["pickup_suburb"])) {  
                    $pickup_suburbErr = "Please enter a suburb for pickup";  
                }
                if (empty($_POST["preferred_date"])) {  
                    $preferred_dateErr = "Please select a date for pickup";  
                }
                if (empty($_POST["preferred_time"])) {
                    $preferred_timeErr = "Please select a time for pickup";
                }
                if (empty($_POST["receiver_name"])) {
                    $receiver_nameErr = "Please enter name of a receiver";
                }
                if (empty($_POST["delivery_address"])) {
                    $delivery_addressErr = "Please enter an address for delivery";
                }
                if (empty($_POST["delivery_suburb"])) {
                    $delivery_suburbErr = "Please enter a suburb for delivery";
                }
                if (empty($_POST["state"])) {
                    $stateErr = "Please enter a state";
                }
                if (!empty($_POST["preferred_time"]) && !empty($_POST['preferred_date'])) {  
                    $time_picked = $_POST["preferred_date"]." ".$_POST['preferred_time'].":00";
                    $current_datetime = strtotime(date("d-m-Y H:i:s"));
                    $timediff = strtotime($time_picked) -$current_datetime;
        
                    if($timediff > 86400){
                        $time = explode(":", $_POST['preferred_time']);
                        $check_time = mktime($time[0], $time[1], 00);
                        $low = mktime(7, 30, 00);
                        $high = mktime(20, 30, 00);
                        if($check_time > $high || $check_time < $low){
                            $preferred_dateErr ='Please pick delivery time between 7:30 and 20:30 only!';
                        }
                    }
                    else if($timediff < 0){
                        $preferred_timeErr = 'Pick up time cannot be before the current time';
                    }
                    else{
                        $preferred_timeErr = 'Pick up Time has to be at least 24 hours later of the current time';
                    }
                }
            }
            
            if(empty($descriptionErr) && empty($weightErr) && empty($pickup_addressErr) && empty($pickup_suburbErr) && empty($preferred_dateErr) && empty($preferred_timeErr) && empty($receiver_nameErr) && empty($delivery_addressErr) && empty($delivery_suburbErr) && empty($stateErr)){
                
                $servername = "feenix-mariadb.swin.edu.au";
            $username = "s104081193";
            $password = "300996";
            $dbname = "s104081193_db";
                $customer_number = $_SESSION['customer_number'];
                $item_description = $_POST['description'];
                $item_weight = $_POST['weight'];
                $pickup_address = $_POST['pickup_address'];
                $pickup_suburb = $_POST['pickup_suburb'];
                $preferred_date = $_POST['preferred_date'];
                $preferred_time = $_POST['preferred_time'];
                $receiver_name = $_POST['receiver_name'];
                $delivery_address = $_POST['delivery_address'];
                $delivery_suburb = $_POST['delivery_suburb'];
                $state = $_POST['state'];
                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
                }
                $sql = "INSERT into request (customer_number, item_description, item_weight, pickup_address, pickup_suburb, pickup_date, pickup_time, receiver_name, delivery_address,delivery_suburb, delivery_state) VALUES('$customer_number', '$item_description','$item_weight','$pickup_address','$pickup_suburb' ,'$preferred_date','$preferred_time','$receiver_name','$delivery_address','$delivery_suburb','$state')";
                if(mysqli_query($conn, $sql)){
                    $sql2= "SELECT * from request ORDER BY request_date DESC";
                    $result = mysqli_query($conn, $sql2);
                    $data = mysqli_fetch_assoc($result);
                    mysqli_free_result($result);
                    if($data['item_weight'] <=2){
                        $cost = 10;
                    }
                    else{
                        $cost = 10 + ($data['item_weight']-2)*2;
                    }
                    $get_customer = "SELECT customer_name, email from customer where customer_number=$customer_number";
                    $get_user = mysqli_query($conn, $get_customer);
                    $user = mysqli_fetch_assoc($get_user);
                    $to = $user['email'];
                    $subject = 'shipping request with ShipOnline';
                    $message = 'Dear '.$user['customer_name'].', Thank you for using ShipOnline! Your request number is '.$data['request_number'].'. The cost is $'.$cost.'. We will pick-up the item at '.$preferred_time.' on '.$preferred_date.'.';
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: <104081193@student.swin.edu.au>' . "\r\n";
                    $mail_check = mail($to,$subject,$message,$headers,"-r 104081193@student.swin.edu.au");
                    if($mail_check){
                        $success_message = 'Thank you! Your request number is '.$data['request_number'].'. The cost is $'.$cost.'. We will pick-up the item at '.$preferred_time.' on '.$preferred_date.'.';
                    }
                    else{
                        $descriptionErr = 'Failed to send Email';
                    }
                }
                else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                mysqli_close($conn);
            }
        }

        

    ?>
    <div class="container">
        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/formdata">
                Item Information: <br />
                <div class="border-form">
                    <div class="form-element">
                        <label for="description" class="form-label">Description:</label>
                        <input type="text" name="description" class="form-control" id="description">
                    </div>
                    <div class="form-element">
                        <label for="weight" class="form-label">Weight:</label>
                        <select name="weight" id="weight">
                            <option value="" selected="true" disabled="disabled">Select Weight</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                        </select>
                        <small>$10 for up to 2 kg and $2 for each additional kg</small>
                    </div>
                </div>
                 Pick-up Information: <br />
                <div class="border-form">
                    <div class="form-element">
                        <label for="pickup_address" class="form-label">Pick-up Address:</label>
                        <input type="text" name="pickup_address" class="form-control" id="pickup_address">
                    </div>
                    <div class="form-element">
                        <label for="pickup_suburb" class="form-label">Suburb:</label>
                        <input type="text" name="pickup_suburb" class="form-control" id="pickup_suburb">
                    </div>
                    <div class="form-element">
                        <label for="preferred_date" class="form-label">Preferred Date:</label>
                        <input type="date" name="preferred_date" id="preferred_date">

                        <div class="form-element">
                        <label for="preferred_time" class="form-label">Time:</label>
                        <input type="time" name="preferred_time" class="form-control" id="preferred_time">
                    </div>
                    </div>
                </div>
                 Delivery Information: <br />
                <div class="border-form">
                    <div class="form-element">
                        <label for="receiver_name" class="form-label">Receiver Name:</label>
                        <input type="text" name="receiver_name" class="form-control" id="receiver_name">
                    </div>
                    <div class="form-element">
                        <label for="delivery_address" class="form-label">Address:</label>
                        <input type="text" name="delivery_address" class="form-control"  id="delivery_address">
                    </div>
                    <div class="form-element">
                        <label for="delivery_suburb" class="form-label">Suburb:</label>
                        <input type="text" name="delivery_suburb" class="form-control"  id="delivery_suburb">
                    </div>
                    <div class="form-element">
                        <label for="state" class="form-label">State:</label>
                        <select name="state" id="state">
                            <option value="" selected="true" disabled="disabled">Select State</option>
                            <option value="ACT">ACT</option>
                            <option value="NSW">NSW</option>
                            <option value="NT">NT</option>
                            <option value="QLD">QLD</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="VIC">VIC</option>

                        </select>
                    </div>
                </div>
                <button type="submit" value="submit" name="submit" class="btn btn-primary" style="margin-top:20px;">Request</button>
                <br />
                <br />
                <?php if(!empty($descriptionErr)){ ?>
                    <span class="error">* <?php echo $descriptionErr; ?> </span><br/>
                    <?php
                    }
                ?>
                <?php if(!empty($weightErr)){ ?>
                    <span class="error">* <?php echo $weightErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($pickup_addressErr)){ ?>
                    <span class="error">* <?php echo $pickup_addressErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($pickup_suburbErr)){ ?>
                    <span class="error">* <?php echo $pickup_suburbErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($preferred_dateErr)){ ?>
                    <span class="error">* <?php echo $preferred_dateErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($preferred_timeErr)){ ?>
                    <span class="error">* <?php echo $preferred_timeErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($receiver_nameErr)){ ?>
                    <span class="error">* <?php echo $receiver_nameErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($delivery_addressErr)){ ?>
                    <span class="error">* <?php echo $delivery_addressErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($delivery_suburbErr)){ ?>
                    <span class="error">* <?php echo $delivery_suburbErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($stateErr)){ ?>
                    <span class="error">* <?php echo $stateErr; ?> </span></br>
                    <?php
                    }
                ?>
                <?php if(!empty($success_message)){ ?>
                <span class="success"><?php echo $success_message; ?> </span>  </br>
                <?php
                }
                ?>
            </form>
        </div>
        <a href="shiponline.php" class="d-flex justify-content-center">Home</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>