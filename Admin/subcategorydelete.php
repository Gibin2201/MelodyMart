<?php
include("../dboperation.php");
$obj=new dboperation();

  $lid=$_GET["lid"];
  $sql="delete from tbl_subcategory where subcategory_id=$lid";
  $res=$obj->executequery($sql);
 
  
  echo "<script>alert('Deleted Successfully!!');window.location='subcategoryview.php'</script>";

?>