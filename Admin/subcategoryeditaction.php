<?php
include_once("../dboperation.php");
$obj=new dboperation();
if (isset($_POST['submit']))
{
    $lid=$_POST['subcategory_id'];
    $subcategory_name=$_POST['subcategoryname'];
    $subcategory_description=$_POST['description'];
    
  $sql= "UPDATE tbl_subcategory
SET name='$subcategory_name',
    description='$subcategory_description'
WHERE subcategory_id=$lid";

    $result=$obj->executequery($sql);
    }
 if (isset($_FILES['image']) &&
        $_FILES['image']['error'] === UPLOAD_ERR_OK)
    {
        $photo = basename($_FILES['image']['name']);

        $uploadPath = "../uploads/";
        $destination = $uploadPath . $photo;

        if (move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $destination
        ))
        {
            $photo = mysqli_real_escape_string(
                $obj->con,
                $photo
            );

            $sqlImage = "UPDATE tbl_subcategory
                         SET image='$photo'
                         WHERE subcategory_id=$lid";

            $resultImage = $obj->executequery($sqlImage);

            if (!$resultImage)
            {
                echo "Image database update failed.";
                exit;
            }
        }
        else
        {
            echo "Image upload failed.";
            exit;
        }
    }



    if ($result == 1){
     echo "<script>alert('Saved Succesfully');window.location='subcategoryview.php' </script>";
    }
    else{
     echo "<script>alert('Registration failed');window.location='subcategoryview.php' </script>";
    }
?>