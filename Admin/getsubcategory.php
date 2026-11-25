<?php
	if(isset($_POST["categoryid"])) 
	{
		$categoryid = $_POST["categoryid"];

		// You can replace this code with a database query to retrieve the states for the selected country
		include_once("../dboperation.php");
        $sql="select * from tbl_subcategory where category_id=$categoryid";
        $obj=new dboperation();
        $result=$obj->executequery($sql);
        $s=1;
?>

<?php
while($row=mysqli_fetch_array($result))
{
?>

<tr>
        <td><?php echo $s++; ?></td>
        <td><?php echo $row["name"]; ?></td>
         <td> <img src="../uploads/<?php echo rawurlencode(basename($row['image'])); ?>" alt="Subcategory Image" style="width:80px; height:60px; object-fit:cover; border-radius:8px;" > </td>
          <td><?php echo $row["description"]; ?></td>
       
        <td>
          <a href="subcategoryedit.php?lid=<?php echo $row["subcategory_id"];?>" class="btn btn-primary">Edit</a>
          <a href="subcategorydelete.php?lid=<?php echo $row["subcategory_id"];?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
        </td>
      </tr>
      
      
      
      <?php
}
	}
?>
