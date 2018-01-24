<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php
$admin = find_admin_by_id($_GET['id']);
if (!$admin) {
  redirect_to('manage_admins.php');
}

if (isset($_POST['submit'])) {
  $required_fields = ['username', 'password'];
  validate_presences($required_fields);

  $fields_with_max_lengths = ['username' => 30];
  validate_max_lengths($fields_with_max_lengths);

  if (empty($errors)) {
    $id = $admin['id'];
    $username = mysql_prep($_POST['username']);
    $hashed_password = password_encrypt($_POST['password']);

    $query = "UPDATE admins SET ";
    $query .= "username = '{$username}', ";
    $query .= "hashed_password = '{$hashed_password}' ";
    $query .= "WHERE id = {$id} ";
    $query .= "LIMIT 1";
    $result = mysqli_query($connection, $query);
    if ($result) {
      $_SESSION['message'] = "Admin updated!";
      redirect_to('manage_admins.php');
    } else {
      $_SESSION['message'] = "Admin update failed :(";
    }
  }
} //end: if(isset($_POST['submit']))
 ?>

<?php $layout_context = 'admin'; ?>
<?php include('../includes/layouts/header.php'); ?>
<div id="main">
  <div id="navigation">
    &nbsp;
  </div>
  <div id="page">
    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>

    <h2>Create Admin</h2>
    <form action="edit_admin.php?id=<?php echo $admin['id']; ?>" method="POST">
      <p>Username:
        <input type="text" name="username" value="<?php echo htmlentities($admin['username']); ?>" />
      </p>
      <p>Password:
        <input type="password" name="password" value="" />
      </p>
      <input type="submit" name="submit" value="Update Admin!">
    </form>
    <br />
    <a href="manage_admins.php">Cancel</a>
  </div>
</div>
<?php include('../inclues/layouts/footer.php'); ?>
