<?php
/**
 * @package Author_Showcase
 * @version 1.4.3
 */
/*
Plugin Name: Author Showcase
Plugin URI: https://claireryanauthor.com/author-showcase
Description: The Author Showcase is an all-in-one plugin for displaying an author's books on their site in a variety of convenient formats, using shortcodes or widgets.
Author: Claire Ryan
Version: 1.4.3
Author URI: https://claireryanauthor.com/
License: GPL v.2
Text Domain: author-showcase
Domain Path: /lang
*/
/*  Copyright 2017 CLAIRE RYAN

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include(dirname(__FILE__) . "/class_bb_admin_list_table.php");
include(dirname(__FILE__) . "/btbe_display_functions.php");
 
class Author_Showcase_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_text', 'description' => __('Add your books here', 'author-showcase'));
		parent::__construct('Author_Showcase_Widget', __('Author Showcase', 'author-showcase'), $widget_ops);
	}

	public function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );
		
		$books = array();
		if(isset($instance['book_ids'])) {
			$books = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}buybooks WHERE id IN (".esc_sql($instance['book_ids']).")", ARRAY_A);		
		}
		if ($instance['other_fields'] != 'null' && $instance['other_fields'] != NULL) {
		  $instance['other_fields'] = json_decode($instance['other_fields'], true);
		}
		else {
			$instance['other_fields'] = array();
		}
		$bookHTML = "";
		if(!empty($books) && $instance['randomize'] != 1) {
			$bookHTML = btbe_generateWidgetDisplay($books, $instance['other_fields'], $instance['book_ids'], $instance['icons_visible']);
		}
		if(!empty($books) && $instance['randomize'] == 1) {
			$randombookid = array_rand($books);
			$bookHTML = btbe_generateRandomWidgetDisplay($books[$randombookid], $instance['other_fields'], $instance['icons_visible']);
		}
		$header = apply_filters( 'widget_header', $instance['header'] );
		
		$before_widget = '<div class="buybook widget">';
		$after_widget = '</div><div style="clear:both;"></div>';
		$before_header = '<h3>';
		$after_header = '</h3>';

		echo $before_widget;
		if ( ! empty( $header ) ) {
			echo $before_header . esc_attr($header) . $after_header;		
		}
		echo $bookHTML;
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['header'] = esc_html(strip_tags( $new_instance['header'] ));
		$instance['book_ids'] = esc_html(strip_tags( $new_instance['book_ids'] ));
		$instance['other_fields'] = json_encode( $new_instance['other_fields'] );
		$instance['randomize'] = esc_html( $new_instance['randomize'] );
		$instance['icons_visible'] = esc_html( $new_instance['icons_visible'] );	
		return $instance;
	}

	public function form( $instance ) {
		global $wpdb;
		if ( isset( $instance[ 'header' ] ) ) {
			$header = $instance[ 'header' ];
		}
		else {
			$header = __( 'Author Showcase', 'author-showcase' );
		}
		$columns = array();
		foreach ( $wpdb->get_col( "DESC " . $wpdb->prefix."buybooks", 0 ) as $column_name ) {
			if(in_array($column_name, array('id', 'services', 'cover', 'created', 'updated', 'blurb', 'book_page'))) {
				continue;
			}
		  $columns[] = $column_name;
		}
		if ($instance['other_fields'] != 'null' && $instance['other_fields'] != NULL) {
		  $instance['other_fields'] = json_decode($instance['other_fields'], true);
		}
		else {
			$instance['other_fields'] = array();
		}
		?>
		<style>
			.btbinstructions ul { list-style-type: disc; margin-left: 2%; }
			.btbinstructions li { margin-left: 1%; list-style-position: outside; } 
		</style>
		<div class="btbinstructions">
		<h4><?php echo __('Instructions','author-showcase'); ?></h4>
		<ul>
			<li><?php echo __('Add a comma-separated list of Book IDs.', 'author-showcase'); ?></li>
			<li><?php echo __('Choose what other fields should appear apart from the cover and the sales links.','author-showcase'); ?></li>
			<li><?php echo __('Tick \'Randomize!\' to show one random book at a time on each page reload.','author-showcase'); ?></li>
			<li><?php echo __('Hit save!','author-showcase'); ?></li>
		</ul>
		</div>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'header' )); ?>"><?php echo __( 'Header', "author-showcase" ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'header' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'header' )); ?>" type="text" value="<?php echo esc_attr( $header ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'book_ids' ); ?>"><?php echo __( 'Book IDs (Comma Separated List)'.$y, "author-showcase" ); ?></label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'book_ids' )); ?>" type="text" value="<?php echo esc_attr( $instance['book_ids'] ); ?>" />
		</p>
		<label class="widefat" for="<?php echo $this->get_field_id( 'other_fields' ); ?>"><?php echo __('Show Other Fields', "author-showcase"); ?></label> 
		<?php foreach ($columns as $column) : ?>
		<p><input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'other_fields' )); ?>[]" type="checkbox" value="<?php echo $column; ?>" <?php if(in_array($column, $instance['other_fields'])) : ?>checked<?php endif; ?>/> <?php $column = ucfirst(str_replace('_', ' ', $column)); echo __($column, 'author-showcase'); ?></p>
		<?php endforeach; ?>
		<label class="widefat" for="<?php echo $this->get_field_id( 'randomize' ); ?>"><?php echo __('Randomize!', "author-showcase"); ?></label>
		<p><input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'randomize' )); ?>" type="checkbox" value="1" <?php if($instance['randomize'] == 1) : ?>checked<?php endif; ?>/><?php echo __('Show a random book from the ID selection above','author-showcase'); ?></p>
		<label class="widefat" for="<?php echo $this->get_field_id( 'icons_visible' ); ?>"><?php echo __('Sales Link Icons',"author-showcase"); ?></label>
		<p><input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'icons_visible' )); ?>" type="checkbox" value="1" <?php if($instance['icons_visible'] == 1) : ?>checked<?php endif; ?>/><?php echo __('Visible by default? (This disables the slideout menu behavior)','author-showcase'); ?></p>
		<?php 
	}

}

class Author_Showcase_Admin extends Btb_List_Table {
	
  function __construct() {
		parent::__construct( array(
			'singular'=> 'bb_book_item',
			'plural' => 'bb_book_items', 
			'ajax'   => false
		) );
	}
	 
	function get_columns() {
		return $columns= array(
			'id' => __('Book ID', 'author-showcase'),
			'title'=>__('Title', 'author-showcase'),
			'subtitle'=>__('Subtitle', 'author-showcase'),
			'author'=>__('Author', 'author-showcase'),
			'series'=>__('Series', 'author-showcase'),
			'cover'=>__('Cover', 'author-showcase'),
			'updated' =>__('Last Update', 'author-showcase'),
			'links'=>__('Sales Links', 'author-showcase'),
			'book_link'=> __('Book Page', 'author-showcase')
		);
	}
	public function get_sortable_columns() {
		return $sortable = array(
			'title'=>'title',
		);
	}
	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();
		$query = "SELECT * FROM {$wpdb->prefix}buybooks";
 
		$totalitems = $wpdb->query($query); 
		$perpage = 10;
		$paged = !empty($_GET["paged"]) ? (int)$_GET["paged"] : '';
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		$totalpages = ceil($totalitems/$perpage);
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}
 
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;
 
		$this->items = $wpdb->get_results($query);
	}
	function display_rows() {
		
		$records = $this->items;
		$columns = $this->get_columns();
		echo '<tr>';
		foreach ($columns as $column_display_name) {
			echo '<th>'.$column_display_name.'</th>';
		}
		echo '</tr>';
		//Loop for each record
		if(!empty($records))	{
			foreach($records as $rec)	{
				//Open the line
				 echo '<tr id="record_'.$rec->id.'">';
					foreach ( $columns as $column_name => $column_display_name ) {
		
					//Style attributes for each col
					$class = "class='$column_name column-$column_name'";
					$style = "";
		
					//edit link
					$editlink  = 'admin.php?page=btbe_add&btbe_id='.(int)$rec->id;
					
					$services = json_decode($rec->services, true);
					$service_string = "";
					if(!empty($services)) {
						foreach ($services as $s) {
							$service_string .= '<a href="'.$s['link'].'" target="_blank"><img height="32" width="32" src="'.$s['icon'].'" /></a>';
						}
					}
					if($rec->book_page != '') {
						$booklink = '<a href="'.get_page_link($rec->book_page).'" target="_blank">'.__('View Book Page', 'author-showcase').'</a>';
					}
					else {
						$booklink = '';
					}
		
					//Display the cell
					switch ( $column_name ) {
						case "id": echo '<td>'.stripslashes($rec->id).'</td>'; break;
						case "title": echo '<td>'.stripslashes($rec->title).'</td>'; break;
						case "subtitle": echo '<td>'.stripslashes($rec->subtitle).'</td>'; break;
						case "author": echo '<td>'.stripslashes($rec->author).'</td>'; break;
						case "series": echo '<td>'.stripslashes($rec->series).' '.stripslashes($rec->series_num).'</td>'; break;
						case "cover": echo '<td><img height="80" width="50" src="'.$rec->cover.'" /></td>'; break;
						case "updated": echo '<td>'.date("D, M jS Y H:ia", strtotime($rec->updated)).'<br /><a href="'.$editlink.'">'.__('Edit this book', 'author-showcase').'</a></td>'; break;
						case "links": echo '<td>'.$service_string.'</td>'; break;
						case "book_link": echo '<td>'.$booklink.'</td>'; break;
					}
				}
				//Close the line
				echo'</tr>';
			}
		}
	}
} // class Author_Showcase_Widget

function register_author_showcase() {
    register_widget('Author_Showcase_Widget');
}

function btbe_enqueuestylesandjs(){
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style('bookstyle', plugins_url('style.css', __FILE__));
	wp_enqueue_script('bookscript', plugins_url('buybook.js', __FILE__));
}

function btbe_install() {
	global $wpdb;
	$table_name = $wpdb->prefix."buybooks";
	$bb_params = $wpdb->prefix."bb_apis";
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'" ) != $table_name){
    $sql= "CREATE TABLE $table_name (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
					created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
					updated DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
					title VARCHAR(300) DEFAULT '' NOT NULL,
					subtitle VARCHAR(300) DEFAULT '' NOT NULL,
					series VARCHAR(300) DEFAULT '' NOT NULL,
					author VARCHAR(300) DEFAULT '' NOT NULL,
					cover VARCHAR(300) DEFAULT '' NOT NULL,
					blurb TEXT DEFAULT '' NOT NULL,
					asin VARCHAR(20) DEFAULT '' NOT NULL,
					isbn VARCHAR(20) DEFAULT '' NOT NULL,
					services TEXT DEFAULT '' NULL,
					book_page VARCHAR(20) DEFAULT '' NULL,
					short_blurb VARCHAR(400) DEFAULT '' NULL,
					series_num VARCHAR(20) DEFAULT '' NULL
          );";
    require_once (ABSPATH. 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
	}
	if($wpdb->get_var("SHOW TABLES LIKE '$bb_params'" ) != $bb_params) {
		$sql= "CREATE TABLE $bb_params (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
					api_name VARCHAR(100) DEFAULT '' NOT NULL,
					assoc VARCHAR(300) DEFAULT '' NOT NULL,
					access_key VARCHAR(300) DEFAULT '' NOT NULL,
					secret VARCHAR(300) DEFAULT '' NOT NULL
          );";
		require_once (ABSPATH. 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}
	add_option( 'buybook_db_version', '1.4.3' );
}

function btbe_admin() {
	add_menu_page( __('Author Showcase Book List', 'author-showcase'), __('Author Showcase Book List', 'author-showcase'), 'manage_options', 'btbe_admin', 'btbe_admin_options' );
	add_submenu_page( 'btbe_admin', __('Add Book', 'author-showcase'), __('Add Book', 'author-showcase'), 'manage_options', 'btbe_add', 'btbe_add_options');
	add_submenu_page( 'btbe_admin', __('Add API Keys', 'author-showcase'), __('Add API Keys', 'author-showcase'), 'manage_options', 'btbe_apis', 'btbe_apis_options');
}

function btbe_admin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'author-showcase' ) );
	}
	include(dirname(__FILE__) . "/btbe_admin.php");
}
function btbe_add_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , 'author-showcase') );
	}
	include(dirname(__FILE__) . "/btbe_add.php");
}
function btbe_apis_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'author-showcase' ) );
	}
	include(dirname(__FILE__) . "/btbe_apis.php");
}

function btbe_page_display($atts) {
	global $wpdb;
	$books = array();
	if(isset($atts['books'])) {
		$books = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}buybooks WHERE id IN (".esc_sql($atts['books']).")", ARRAY_A);
	}
	if(isset($atts['series'])) {
		$series = $wpdb->get_results("SELECT DISTINCT series FROM {$wpdb->prefix}buybooks WHERE id IN (".esc_sql($atts['series']).")", ARRAY_A);
		$books = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}buybooks WHERE series LIKE '%".esc_sql($series['series'])."%' ORDER BY created", ARRAY_A);
	}
	if(!empty($books)) {
		return btbe_generatePageDisplay($books, $atts);
	}
}

function btbe_enqueuemedia($page) {
	wp_enqueue_media();
}

function btbe_load_textdomain() {
	load_plugin_textdomain( 'author-showcase', false, basename( dirname( __FILE__ ) ) . '/lang/' );
}

add_action('widgets_init', 'register_author_showcase');
add_action('wp_enqueue_scripts', 'btbe_enqueuestylesandjs');
add_filter('admin_enqueue_scripts', 'btbe_enqueuemedia' );
add_action( 'admin_menu', 'btbe_admin' );
add_shortcode( 'btbe_display', 'btbe_page_display' );
add_action('plugins_loaded', 'btbe_load_textdomain');
register_activation_hook( __FILE__, 'btbe_install' );