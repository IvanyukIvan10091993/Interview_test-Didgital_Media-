<form class="" action="/" method="post">
  <?php foreach($fields as $field) : ?>
  <label for=""><?php echo $field; ?></label><input type="text" name="<?php echo $field; ?>" value="">
  <?php endforeach; ?>
  <input onclick="ajax(this.parentNode)" type="button" name="name" value="Submit">
  <div id="response"></div>
</form>
