<?php
    include_once("../dboperation.php");
    $obj=new dboperation();
    if(isset($_POST['submit']))
    {
    $cusname = mysqli_real_escape_string($obj->con, trim($_POST['custname']));
    $locationid = (int)$_POST['locationid'];
    $phoneno = mysqli_real_escape_string($obj->con, trim($_POST["phoneno"]));
    $email = mysqli_real_escape_string($obj->con, trim($_POST["email"]));
    $username = mysqli_real_escape_string($obj->con, trim($_POST["username"]));
    $password = mysqli_real_escape_string($obj->con, trim($_POST["password"]));
    $regdate = date('Y-m-d');

    // 1. Check if username is already taken
    $sqlUserCheck = "SELECT customer_id FROM tbl_customer WHERE username='$username'";
    $resUserCheck = $obj->executequery($sqlUserCheck);
    if ($resUserCheck && mysqli_num_rows($resUserCheck) > 0) {
        echo "<script>alert('Username is already taken! Please choose a different username.'); window.location='cusreg.php';</script>";
        exit();
    }

    // 2. Check if email is already registered
    $sqlEmailCheck = "SELECT customer_id FROM tbl_customer WHERE email='$email'";
    $resEmailCheck = $obj->executequery($sqlEmailCheck);
    if ($resEmailCheck && mysqli_num_rows($resEmailCheck) > 0) {
        echo "<script>alert('Email is already registered! Please use another email or log in.'); window.location='cusreg.php';</script>";
        exit();
    }

    // 3. Insert unique customer record
    $sqlquery1 = "INSERT INTO tbl_customer(customer_name, location_id, phone_no, email, username, password, reg_date) 
                  VALUES('$cusname', '$locationid', '$phoneno', '$email', '$username', '$password', '$regdate')";
    $result1 = $obj->executequery($sqlquery1);
    if ($result1) {
        $bodyContent = "Dear $cusname, Your registration is completed. Your username is $username and password is $password";
        $mailtoaddress = $email;
        if (file_exists('phpmailer.php')) {
            @include('phpmailer.php');
        }
        echo "<script>alert('Registration Successful! Please login with your credentials.'); window.location='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Registration failed due to a database error. Please try again.'); window.location='cusreg.php';</script>";
        exit();
    }
}
?>