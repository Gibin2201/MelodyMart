<?php
	if(isset($_POST["districtid"])) 
	{
		$districtid = $_POST["districtid"];

		// You can replace this code with a database query to retrieve the states for the selected country
		include_once("../dboperation.php");
        $sql="select * from tbl_location where district_id=$districtid";
        $obj=new dboperation();
        $result=$obj->executequery($sql);
        $s=1;
?>

<?php
while($row=mysqli_fetch_array($result))
{
?>


        <option value="<?php echo $row["location_id"]; ?>"><?php echo $row["location_name"]; ?></option>
        
       
    
      
      
      <?php
}
	}
?>