<?php
include_once("../dboperation.php");
$obj=new dboperation();
if (isset($_POST['submit']))
{
    $lid=$_POST['location_id'];
    $location_name=$_POST['locationname'];
   
$sql="UPDATE tbl_location set location_name='$location_name' where location_id=$lid";
    $result=$obj->executequery($sql);
    }
    if ($result == 1){
     echo "<script>alert('Saved Succesfully');window.location='locationview.php' </script>";
    }
    else{
     echo "<script>alert('Registration failed');window.location='locationview.php' </script>";
    }
?>