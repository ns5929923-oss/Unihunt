<form method="post"> 
  Enter something: <input type="text" name="input"> 
  <input type="submit" value="Submit"> 
</form> 
<?php 
if ($_POST) { 
  $input = $_POST['input']; 
 if (empty($input)) { 
    echo "Input cannot be empty!"; 
  } else { 
    echo "You entered: " . $input; 
  }} 
?> 
