<h1>Author Showcase</h1>
<div style="float: left; width: 50%;">
<p><a href="admin.php?page=btbe_add" class="button"><strong><?php echo __('Add New Book', 'author-showcase'); ?></strong></a></p>
<p>
  <h3><?php echo __('See <a href="https://claireryanauthor.com/btbe-user-manual/" target="_blank">the Showcase User Manual</a> for a complete list of how to use the shortcodes.', 'author-showcase'); ?></h3>
  <span style="color: red"><strong><?php echo __('Make sure to add each book in a series in the order in which they were published!', 'author-showcase'); ?></strong></span><br />
<?php echo __('Use the Author Showcase Widget to display books in the sidebar using a comma-separated list of their Book IDs.', 'author-showcase'); ?>
</p>
</div>
<div style="float: right; width: 50%;">
  <h3><?php echo __('About the plugin', 'author-showcase'); ?></h3>
  <p>
    <?php echo __('Please send feature suggestions, bug reports, and help requests to me through my <a href="http://www.raynfall.com/contact" target="_blank">contact form</a>. If you\'d like to support this plugin, please let other authors know about it! You can also check out my website, <a href="http://www.raynfall.com" target="_blank">Raynfall.com</a>, and take a look at my books.', 'author-showcase'); ?>
  </p>
  <p>
    <?php echo __('Happy selling :)', 'author-showcase'); ?><br />--Claire Ryan
  </p>
</div>
<div class="clear"></div>
  <hr>
<?php

$book_list_table = new Author_Showcase_Admin();
$book_list_table->prepare_items();

?>

  <table class="btbe_admin_table">
    <?php $book_list_table->display(); ?>
  </table>
