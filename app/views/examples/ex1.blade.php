<h1>Hello, <?php echo $name; ?></h1>
<?php echo $v1; ?>
<hr/>
<?php
  foreach ($children as $child){
    echo $$child;
    echo "<hr/>";
  }
?>
