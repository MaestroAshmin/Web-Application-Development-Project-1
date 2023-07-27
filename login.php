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
        $servername = "feenix-mariadb.swin.edu.au";
            $username = "s104081193";
            $password = "300996";
            $dbname = "s104081193_db";
        if(isset($_POST['submit'])){

            if (empty($_POST["customer_number"])) {  
                $customerNumberErr = "Customer Number is Required";  
            } else {  
          
                    $customer_number = $_POST["customer_number"];  

                    if (!preg_match ("/^[0-9]*$/", $customer_number) ) {  
                    $customerNumberErr = "Only numeric value is allowed.";  
                    }  
            } 
            if (empty($_POST["password"])) {  
                $passwordErr = "Password is required";  
            }
            
        }
        else{
            echo 'false';
        }

        if(empty($customerNumberErr) && empty($passwordErr)){
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
            }
            $customer_number = $_POST['customer_number'];
            $customer_password = md5($_POST['password']);
            $check_user = "SELECT * FROM customer WHERE customer_number = '$customer_number'";
            if(mysqli_query($conn, $check_user)){
                $result = mysqli_query($conn, $check_user);
                if(mysqli_num_rows($result) <= 0){
                    $customerNumberErr = 'This user does not exist';
                }
                else{
                    $data = mysqli_fetch_assoc($result);
                    if($customer_password != $data['customer_password']){
                        $passwordErr = 'Wrong Password';
                    }
                    else{
                        session_start();
                        $_SESSION['customer_number'] = $data['customer_number'];
                        $redirect_url = "request.php?customer_number=".$data['customer_number'];
                        header('Location:'.$redirect_url);
                    }
                    
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
        <h2 class="d-flex justify-content-center">ShipOnline System Login Page</h2>
        <div class="form-component">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/formdata">
                <div class="form-element">
                    <label for="contact" class="form-label">Customer Number:</label>
                    <input type="text" name="customer_number" class="form-control" id="customer_number">
                    
                </div>
                <div class="form-element">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" id="password">
                   
                </div>
                <button type="submit" name="submit" value ="submit" class="btn btn-primary">Log in</button>
                <br />
                <?php if(!empty($customerNumberErr)){ ?>
                <span class="error">* <?php echo $customerNumberErr; ?> </span>  
                <?php
                }
                ?>
                <br />
                <?php if(!empty($passwordErr)){ ?>
                <span class="error">* <?php echo $passwordErr; ?> </span>  
                <?php
                }
                ?>
            </form>
        </div>
        <a href="shiponline.php">Home</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>