<?php
require_once('../includes/db_connection.php');
require_once('../includes/functions.php');

if (isset($_POST['submit'])) {
  //// process the form ////
  //grab form input stuff
  $menu_name = mysql_prep($_POST['menu_name']);
  $position = (int) $_POST['position'];
  $visible = (int) $_POST['visible'];
  //build query
  $query = "INSERT INTO subjects (";
  $query .= " menu_name, position, visible";
  $query .= ") VALUES (";
  $query .= " '{$menu_name}', {$position}, {$visible}";
  $query .= ")";
  //make query
  $result = mysqli_query($connection, $query);
  //check result
  if ($result) {
    //SUCCESS
    $message = "Subject created";
    redirect_to('manage_content.php');
  } else {
    //FAILURE
    $message = "Subject creation failed";
    redirect_to('new_subject.php');
  }
} else {
  //this is probably a GET request
  redirect_to('new_subject.php');
}

if (isset($connection)) { mysqli_close($connection); }
