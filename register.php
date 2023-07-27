<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Miketyson123@ -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShipOnline System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        $nameErr = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servername = "feenix-mariadb.swin.edu.au";
            $username = "s104081193";
            $password = "300996";
            $dbname = "s104081193_db";
        if(isset($_POST['submit'])){
            if (empty($_POST["customer_name"])) {  
                $nameErr = "Name is required";  
            } else {  
                $name = $_POST["customer_name"];  
                    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {  
                        $nameErr = "Only alphabets and white space are allowed";  
                    }  
            } 
            if (empty($_POST["password"])) {  
                $passwordErr = "Password is required";  
            } else {  
                $user_password = $_POST["password"];  
                $uppercase = preg_match('@[A-Z]@', $user_password);
                $lowercase = preg_match('@[a-z]@', $user_password);
                $number    = preg_match('@[0-9]@', $user_password);
                $specialChars = preg_match('@[^\w]@', $user_password);

                if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($user_password) < 8) {
                    $passwordErr = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
                }
            } 
            if (empty($_POST["confirm_password"])) { 
                if(empty($user_password)){
                    $confirm_passwordErr = "Password is required";  
                }
                else{
                    $confirm_passwordErr = "Password does not match";
                } 
            } else {
                $confirm_password = $_POST["confirm_password"]; 
                if($user_password !== $confirm_password){
                    $confirm_passwordErr = "Password does not match";  
                }
            }
            if (empty($_POST["email"])) {  
                    $emailErr = "Email is required";  
            } else {  
                    $email = $_POST["email"];  
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
                        $emailErr = "Invalid email format";  
                    }  
            }  
            if (empty($_POST["contact_phone"])) {  
                    $mobilenoErr = "Mobile no is required";  
            }
            else {  
                $mobileno = $_POST["contact_phone"];  
                if (!preg_match ("/^[0-9]*$/", $mobileno) ) {  
                    $mobilenoErr = "Only numeric value is allowed.";  
                }  
                if (strlen ($mobileno) != 10) {  
                    $mobilenoErr = "Mobile no must contain 10 digits.";  
                }  
            }  
        }
        else{
            echo 'false';
        }

        if(empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirm_passwordErr) && empty($mobilenoErr)){
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
            $user_name = $_POST['customer_name'];
            $user_pass = md5($_POST['password']);
            $user_email = $_POST['email'];
            $user_contact = $_POST['contact_phone'];
            $check_user = "SELECT * FROM customer WHERE email = '$user_email'";
            if(mysqli_query($conn, $check_user)){
                $user_check = mysqli_query($conn, $check_user);
                if(mysqli_num_rows($user_check) > 0){
                    $emailErr = 'This email is already a registered user. Please Login to Proceed';
                }
                else{
                    $sql = "INSERT INTO customer (customer_name, customer_password, email, contact_phone_number)
                    VALUES ('$user_name','$user_pass', '$user_email', '$user_contact')";
                    if (mysqli_query($conn, $sql)) {
                        $sql2 = "SELECT customer_number FROM customer WHERE email = '$user_email'";
                        if ($result = mysqli_query($conn, $sql2)) {
                            $data = mysqli_fetch_row($result);
                            mysqli_free_result($result);
                            }
                        $success_message = 'Dear '.$user_name.', you are successfully registered into ShipOnline, and your customer number is '.$data[0].', which will be used to get into the system.';
                    } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    }

                    mysqli_close($conn);
                }
            }
            else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
        
    }
    
    ?>
    <div class="container">
        <div class="title-nav">
            <h2 class="d-flex justify-content-center">ShipOnline System Registration Page</h2>
        </div>
        
        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/formdata">
                <div class="form-element">
                    <label for="name" class="form-label">Name:</label>
                    <input type="name" name="customer_name" class="form-control" id="name">
                    <?php if(!empty($nameErr)){ ?>
                    <span class="error">* <?php echo $nameErr; ?> </span>  
                    <?php
                    }
                    ?>
                </div>
                <div class="form-element">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" id="password">
                    <?php if(!empty($passwordErr)){ ?>
                    <span class="error">* <?php echo $passwordErr; ?> </span>  
                    <?php
                    }
                    ?>
                </div>
                <div class="form-element">
                    <label for="password" class="form-label">Confirm Password:</label>
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password">
                    <?php if(!empty($confirm_passwordErr)){ ?>
                    <span class="error">* <?php echo $confirm_passwordErr; ?> </span>  
                    <?php
                    }
                    ?>
                </div>
                <div class="form-element">
                    <label for="email" class="form-label">Email Address:</label>
                    <input type="email" name="email" class="form-control" id="email">
                    <?php if(!empty($emailErr)){ ?>
                    <span class="error">* <?php echo $emailErr; ?> </span>  
                    <?php
                    }
                    ?>
                </div>
                <div class="form-element">
                    <label for="contact" class="form-label">Contact Phone:</label>
                    <input type="text" class="form-control" name="contact_phone" id="contact_phone">
                    <?php if(!empty($mobilenoErr)){ ?>
                    <span class="error">* <?php echo $mobilenoErr; ?> </span>  
                    <?php
                    }
                    ?>
                </div>
                <button type="submit" value="submit" name="submit" class="btn btn-primary">Register</button>
                <br />
                <br />
                <?php if(!empty($success_message)){ ?>
                <span class="success"><?php echo $success_message; ?> </span>  
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