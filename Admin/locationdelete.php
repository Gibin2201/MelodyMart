<?php
include("../dboperation.php");
$obj=new dboperation();

  $lid=$_GET["lid"];
  $sql="delete from tbl_location where location_id=$lid";
  $res=$obj->executequery($sql);
 
  
  echo "<script>alert('Deleted Successfully!!');window.location='locationview.php'</script>";

?>