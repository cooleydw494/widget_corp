<?php
  // confirm a query got a result (empty set still counts)
  function confirm_query($result_set)
  {
    if (!$result_set)
    {
      die("Database query failed.");
    }
  }
 ?>
