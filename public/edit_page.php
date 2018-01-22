<?php require_once('../includes/session.php') ?>
<?php require_once('../includes/db_connection.php'); ?>
<?php require_once('../includes/functions.php'); ?>
<?php require_once('../includes/validation_functions.php'); ?>
<?php find_selected_page(); ?>
<?php //verify there is a current subject
if (!$current_page) {
  $_SESSION['message'] = 'Error: no page selected';
  redirect_to('manage_content.php');
}
 ?>
<?php //process edit form submission
 if (isset($_POST['submit'])) {

   $required_fields = ['menu_name', 'position', 'visible', 'content'];
   $fields_with_max_lengths = ['menu_name' => 30];
   validate_presences($required_fields);
   validate_max_lengths($fields_with_max_lengths);

   if (empty($errors)) {
     //perform update
     $id = $current_page['id'];
     $content = mysql_prep($_POST['content']);
     $menu_name = mysql_prep($_POST['menu_name']);
     $position = (int) $_POST['position'];
     $visible = isset($_POST['visible']) ? (int) $_POST['visible'] : '';

     $query = "UPDATE pages SET";
     $query .= " menu_name = '{$menu_name}',";
     $query .= " position = {$position},";
     $query .= " visible = {$visible},";
     $query .= " content = '{$content}'";
     $query .= " WHERE id = {$id}";
     $query .= " LIMIT 1";
     //make query
     $result = mysqli_query($connection, $query);
     //check result
     if ($result && mysqli_affected_rows($connection) >= 0) {
       //SUCCESS
       $_SESSION['message'] = "Page Updated";
       redirect_to('manage_content.php');
     } else {
       //FAILURE
       $message = "Page editing failed";
       echo "page edit failed";
     }
   }
 } else { ?>

<?php $layout_context = "admin"; ?>
<?php include('../includes/layouts/header.php'); ?>
<div id="main">
  <div id="navigation">
    <?php echo navigation($current_subject, $current_page); ?>
  </div>
  <div id="page">
    <?php echo message(); ?>
    <?php echo form_errors(errors()); ?>
    <h2>Edit Page</h2>
    <form action="edit_page.php?page=<?php echo urlencode($current_page['id']); ?>" method="post">
      <p>Menu name:
        <input type="text" name="menu_name" value="<?php echo htmlentities($current_page['menu_name']) ?>" />
      </p>
      <br />
      <p>Position:
        <select name="position">
          <?php
          $page_set = find_pages_for_subject($current_page['subject_id'], false);
          $page_count = mysqli_num_rows($page_set);
          for($count = 1; $count <= $page_count; $count++) {
            echo "<option value=\"{$count}\"";
            if ($current_page['position'] == $count) {
              echo " selected";
            }
            echo ">{$count}</option>";
          }
          ?>
        </select>
      </p>
      <p>Visible:
        <input type="radio" name="visible" value="0" <?php echo $current_page['visible'] ? '' : 'checked' ?>/> No
        &nbsp;
        <input type="radio" name="visible" value="1" <?php echo $current_page['visible'] ? 'checked' : '' ?>/> Yes
      </p>
      <p>Content:<br />
        <textarea name="content" rows="20" cols="80"><?php echo htmlentities($current_page['content']); ?></textarea>
      </p>
      <input type="submit" name="submit" value="Update Page" />
    </form>
    <br />
    <a href="manage_content.php?page=<?php echo urlencode($current_page['id']); ?>">Cancel</a>
    <br />
    <a href="delete_page.php?page=<?php echo urlencode($current_page['id']); ?>">Delete</a>
  </div>
</div>
<?php } ?>
<?php include('../includes/layouts/footer.php'); ?>
