<?php
    include_once("../dboperation.php");
    $obj=new dboperation();
    if(isset($_POST['submit']))
    {
    $subcategoryname=$_POST['subcategoryname'];
    $categoryid=$_POST['categoryid'];
    $photo=$_FILES['image']['name'];
    move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/" . $photo);
    $description=$_POST['description'];
    $sqlquery="SELECT * FROM tbl_subcategory where name='$subcategoryname'";
    $result=$obj->executequery($sqlquery);
    $rows=mysqli_num_rows($result);
    if($rows==1)
    {
          echo "<script>alert('Already Exist!!');window.location='subcategory.php'</script>";
    
    }
    else
    {
       $sqlquery1="INSERT INTO tbl_subcategory (name,image,description,category_id) VALUES('$subcategoryname','$photo',' $description','$categoryid')";
        $result1=$obj->executequery($sqlquery1);
        if($result1==1)
        {
          echo "<script>alert('Registration Succesfully!!');window.location='subcategory.php'</script>";
    
        }
        else
        {
        echo "<script>alert('Registration Failed!!');window.location='subcategory.php'</script>";
}
}
}
?>