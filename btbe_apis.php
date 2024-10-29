<h1><?php echo __('Add your API keys for displaying reviews', 'author-showcase'); ?></h1>
<?php
global $wpdb;
$amazon = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bb_apis WHERE api_name = 'amazon'", ARRAY_A);
$amazon = $amazon[0];
$goodreads = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bb_apis WHERE api_name = 'goodreads'", ARRAY_A);
$goodreads = $goodreads[0];
if((!empty($amazon) || !empty($goodreads)) && !isset($_REQUEST['btbe_api'])) {
  $_REQUEST['amazon_assoc'] = @$amazon['assoc'];
  $_REQUEST['amazon_access'] = @$amazon['access_key'];
  $_REQUEST['amazon_secret'] = @$amazon['secret'];
  $_REQUEST['goodreads_key'] = @$goodreads['access_key'];
}
if(@$_REQUEST['btbe_api'] == 1) {
	global $wpdb;
	$new_amazon = array(
		'api_name' => 'amazon',
		'assoc' => esc_sql(@$_REQUEST['amazon_assoc']),
		'access_key' => esc_sql(@$_REQUEST['amazon_access']),
		'secret' => esc_sql(@$_REQUEST['amazon_secret']),
	);
  $new_goodreads = array(
    'api_name' => 'goodreads',
    'assoc' => '',
    'access_key' => esc_sql(@$_REQUEST['goodreads_key']),
    'secret' => '',
  );
  if(!empty($amazon)) {
    $wpdb->update($wpdb->prefix.'bb_apis', $new_amazon, array('id' => esc_sql($amazon['id'])), array('%s','%s','%s','%s'));
  }
  else {
    $wpdb->insert( $wpdb->prefix.'bb_apis', $new_amazon );
  }
  if(!empty($goodreads)) {
    $wpdb->update($wpdb->prefix.'bb_apis', $new_goodreads, array('id' => esc_sql($goodreads['id'])), array('%s','%s','%s','%s'));
  }
  else {
    $wpdb->insert( $wpdb->prefix.'bb_apis', $new_goodreads );
  }
  $btbe_message = __('API keys saved! <a href="admin.php?page=btbe_admin">return to the Book List?</a>', 'author-showcase');
}
?>
<style>
  .btbe_message {
		background: lightblue;
		padding: 0.5em;	
	}
</style>
<?php if ($btbe_message) : ?><h4 class="btbe_message"><?php echo $btbe_message; ?></h4><?php endif; ?>
<form action="" method="post">
  <div class="wrap">
    <table class="form-table btbe_add">
        <tr valign="top"><th scope="row"><?php echo __('Amazon Associate Tag', 'author-showcase'); ?></th>
            <td><input type="text" name="amazon_assoc" value="<?php echo @$_REQUEST['amazon_assoc']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row"><?php echo __('Amazon Access Key', 'author-showcase'); ?></th>
            <td><input type="text" name="amazon_access" value="<?php echo @$_REQUEST['amazon_access']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row"><?php echo __('Amazon Secret Key', 'author-showcase'); ?></th>
            <td><input type="text" name="amazon_secret" value="<?php echo @$_REQUEST['amazon_secret']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row"><?php echo __('Goodreads API Key (you can get a key <a href="https://www.goodreads.com/api/keys">here</a>)', 'author-showcase'); ?></th>
            <td><input type="text" name="goodreads_key" value="<?php echo @$_REQUEST['goodreads_key']; ?>" /></td>
        </tr>
        <tr>
          <td>
            <p class="submit">
              <input type="hidden" name="btbe_api" value="1" />
                <input type="submit" class="button-primary" value="<?php echo __('Save', 'author-showcase'); ?>" />
            </p>
          </td>
        </tr>
    </table>
  </div>
</form>