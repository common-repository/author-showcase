<?php
global $wpdb;
$series = $wpdb->get_results("SELECT DISTINCT series FROM {$wpdb->prefix}buybooks WHERE series NOT LIKE ''", ARRAY_A);
if (@$_REQUEST['btbe_id'] > 0 && @$_REQUEST['realdelete'] == 1) {
    $wpdb->delete($wpdb->prefix . 'buybooks', array('id' => $_REQUEST['btbe_id']), '%d');
    echo '<h3>Book deleted.</h3><p><a href="admin.php?page=btbe_admin">Return to the Book List</a></p>';
    exit;
}

if (@$_REQUEST['btbe_add'] == 1) {

    if (empty($_REQUEST['title'])) {
        $btbe_message = __("You must enter a title!", 'author-showcase');
    } else {
        if (isset($_REQUEST['cover'])) {
            $spliturl = parse_url(esc_url($_REQUEST['cover']));
            $path = $spliturl['path'];
        } else {
            $path = '';
        }
        $book = array(
            'created' => date('Y-m-d H:i:s', time()),
            'updated' => date('Y-m-d H:i:s', time()),
            'title' => esc_attr(@$_REQUEST['title']),
            'subtitle' => isset($_REQUEST['subtitle']) ? esc_attr(@$_REQUEST['subtitle']) : '',
            'author' => isset($_REQUEST['author']) ? esc_attr(@$_REQUEST['author']) : '',
            'series' => isset($_REQUEST['series']) ? esc_attr(@$_REQUEST['series']) : '',
            'series_num' => isset($_REQUEST['series_num']) ? esc_attr(@$_REQUEST['series_num']) : '',
            'blurb' => isset($_REQUEST['blurb']) ? esc_textarea(@$_REQUEST['blurb']) : '',
            'cover' => $path,
            'asin' => isset($_REQUEST['asin']) ? esc_attr(@$_REQUEST['asin']) : '',
            'isbn' => isset($_REQUEST['isbn']) ? esc_attr(@$_REQUEST['isbn']) : '',
            'book_page' => isset($_REQUEST['book_page']) ? esc_attr(@$_REQUEST['book_page']) : '',
            'short_blurb' => isset($_REQUEST['short_blurb']) ? esc_textarea(@$_REQUEST['short_blurb']) : '',
        );

        $services = array();

        foreach ($_REQUEST as $idx => $r) {
            if (strpos($idx, 'sername') !== false) {
                $int = filter_var($idx, FILTER_SANITIZE_NUMBER_INT);
                $name = isset($_REQUEST['sername' . $int]) ? esc_attr(@$_REQUEST['sername' . $int]) : '';
                $icon = isset($_REQUEST['sericon' . $int]) ? esc_attr(@$_REQUEST['sericon' . $int]) : '';
                $link = isset($_REQUEST['serlink' . $int]) ? esc_url(@$_REQUEST['serlink' . $int]) : '';
                $services[] = array(
                    'name' => $name,
                    'icon' => $icon,
                    'link' => $link
                );
            }
        }
        $book['services'] = json_encode($services);
        if (@$_REQUEST['btbe_id'] != "") {
            unset($book['created']);
            $wpdb->update($wpdb->prefix . 'buybooks', $book, array('id' => esc_sql($_REQUEST['btbe_id'])), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));
        } else {
            $wpdb->insert($wpdb->prefix . 'buybooks', $book, array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));
            $_REQUEST['btbe_id'] = $wpdb->insert_id;
        }
        $btbe_message = __('Book saved!', 'author-showcase') . ' ' . __('<a href="admin.php?page=btbe_add">Add another</a> or <a href="admin.php?page=btbe_admin">return to the Book List?</a>', 'author-showcase');
    }
}
if (@$_REQUEST['btbe_id'] > 0 && !isset($_REQUEST['btbe_add'])) {
    $record = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}buybooks WHERE id = '" . esc_sql($_REQUEST['btbe_id']) . "'", ARRAY_A);
    if (!$record) {
        echo 'Book not found!';
        exit;
    }
    $_REQUEST = array_merge($_REQUEST, $record);
    $services = json_decode($record['services'], true);
}

?>
<script>
    jQuery(document).ready(function () {
        var file_frame;
        var btbe_cover = "";
        var services_count = <?php echo isset($services) && count($services) > 0 ? count($services) + 1 : 0; ?>;
        var currentimgid = "";
        jQuery('.img_upload').on('click', function (event) {
            event.preventDefault();
            currentimgid = event.target.id.replace("_button", "");
            if (file_frame) {
                file_frame.open();
                return;
            }
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery(this).data('uploader_title'),
                button: {
                    text: jQuery(this).data('uploader_button_text'),
                },
                multiple: false
            });
            file_frame.on('select', function () {
                attachment = file_frame.state().get('selection').first().toJSON();
                jQuery("#" + currentimgid).val(attachment.sizes.full.url);
                if (currentimgid === "cover_image") {
                    jQuery("#btbe_cover").html('<img src="' + attachment.sizes.full.url + '" />');
                    btbe_cover = attachment.sizes.full.url;
                } else {
                    jQuery('#' + currentimgid + '_icon').remove();
                    jQuery('#' + currentimgid + '_button').after('<img id="' + currentimgid + '_icon" src="' + attachment.sizes.full.url + '" width="32" height="32" />');
                }
            });

            file_frame.open();
        });
    });

    function btbe_addservice(event) {
        event.preventDefault();
        jQuery('#btbe_services_table').append('<tr id="row' + services_count + '"><td><input name="sername' + services_count + '" type="text" /></td><td><input type="hidden" id="sericon' + services_count + '" class="img_upload" name="sericon' + services_count + '" /><button class="img_upload" id="sericon' + services_count + '_button">Upload Icon</button></td><td><input type="text" name="serlink' + services_count + '" /></td><td><button onclick="btbe_remove(event, \'row' + services_count + '\');">Remove</button></td></tr>');
        services_count++;
    }

    function btbe_remove(event, id) {
        event.preventDefault();
        jQuery('#' + id).remove();
    }

    function btbe_checktitle(event) {
        var title = jQuery('#btbe_title').val();
        if (title.length == 0) {
            event.preventDefault();
            alert("You must add a book title!");
            return;
        }
    }

    function btbe_dropin_series() {
        jQuery('#btbe_series').val(jQuery('#btbe_series_select').val());
    }
</script>
<style>
    table.btbe_add {
        width: 50%;
        float: left;
    }

    #btbe_cover {
        float: right;
        width: 40%;
    }

    #btbe_cover img {
        width: 100%;
        height: auto;
    }

    .btbe_add input, .btbe_add textarea {
        width: 100%;
    }

    table.btbe_services {
        font-family: verdana, arial, sans-serif;
        font-size: 11px;
        color: #333333;
        border-width: 1px;
        border-color: #a9c6c9;
        border-collapse: collapse;
    }

    table.btbe_services th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #a9c6c9;
    }

    table.btbe_services td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #a9c6c9;
    }

    .btbe_services img {
        margin-left: 10px;
        margin-top: 5px;
    }

    .btbe_message {
        background: lightblue;
        padding: 0.5em;
    }

    .btbe_delete {
        background: pink;
        padding: 0.5em;
    }

</style>
<?php if (@$_REQUEST['btbe_id'] != "") : ?>
    <h1>Update this Book</h1>
<?php else : ?>
    <h1>Add a Book</h1>
<?php endif; ?>
<?php if ($btbe_message) : ?><h4 class="btbe_message"><?php echo $btbe_message; ?></h4><?php endif; ?>
<?php if (@$_REQUEST['btbe_id'] > 0 && @$_REQUEST['delete'] > 0) : ?>
    <h4 class="btbe_delete"><?php echo __('Are you absolutely sure you want to delete this book?', 'author-showcase'); ?>
        <a href="<?php echo admin_url('admin.php?page=btbe_add&btbe_id=' . $_REQUEST['btbe_id'] . '&realdelete=1'); ?>"
           class="button"><?php echo __('Yes, delete it!', 'author-showcase'); ?></a></h4>
<?php endif; ?>
<form action="" method="post">
    <div class="wrap">
        <table class="form-table btbe_add">
            <?php if (@$_REQUEST['btbe_id'] != "") : ?>
                <input type="hidden" name="btbe_id" value="<?php echo $_REQUEST['btbe_id']; ?>" required/>
            <?php endif; ?>
            <tr valign="top">
                <th scope="row"><?php echo __('Book Title', 'author-showcase'); ?></th>
                <td><input type="text" name="title" id="btbe_title"
                           value="<?php echo stripslashes(@$_REQUEST['title']); ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Subtitle', 'author-showcase'); ?></th>
                <td><input type="text" name="subtitle" value="<?php echo stripslashes(@$_REQUEST['subtitle']); ?>"/>
                </td>
            </tr>
            <?php if (!empty($series)) : ?>
                <tr valign="top">
                    <th scope="row"><?php echo __('Series (Create a new series, or add to an existing series)', 'author-showcase'); ?></th>
                    <td><input type="text" id="btbe_series" name="series"
                               value="<?php echo stripslashes(@$_REQUEST['series']); ?>"/>
                        <select onchange="btbe_dropin_series();" id="btbe_series_select">
                            <option value=""><?php echo __('-Choose an existing series-', 'author-showcase'); ?></option>
                            <?php foreach ($series as $s) : ?>
                                <option value="<?php echo $s['series']; ?>"><?php echo stripslashes($s['series']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php else : ?>
                <tr valign="top">
                    <th scope="row"><?php echo __('Series', 'author-showcase'); ?></th>
                    <td><input type="text" name="series" value="<?php echo stripslashes(@$_REQUEST['series']); ?>"/>
                    </td>
                </tr>
            <?php endif; ?>
            <tr valign="top">
                <th scope="row"><?php echo __('Series Number (ex. 1, 2, #1, #2, One, Two)', 'author-showcase'); ?></th>
                <td><input type="text" name="series_num" value="<?php echo stripslashes(@$_REQUEST['series_num']); ?>"/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Author', 'author-showcase'); ?></th>
                <td><input type="text" name="author" value="<?php echo stripslashes(@$_REQUEST['author']); ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Short Blurb', 'author-showcase'); ?>
                    <br/><small><?php echo __('(This is used in the sidebar and the list format - limit 400 characters)', 'author-showcase'); ?></small>
                </th>
                <td><textarea name="short_blurb"
                              rows="5"><?php echo stripslashes(@$_REQUEST['short_blurb']); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Blurb', 'author-showcase'); ?>
                    <br/><small><?php echo __('(This is used in the single format)', 'author-showcase'); ?></small></th>
                <td><textarea name="blurb" rows="5"><?php echo stripslashes(@$_REQUEST['blurb']); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Link to a Book Page?', 'author-showcase'); ?>
                    <br/><small><?php echo __('(This is used in the sidebar and the grid format slideout menus)', 'author-showcase'); ?></small>
                </th>
                <?php $selectpage = __('-Select a page-', 'author-showcase'); ?>
                <td><?php wp_dropdown_pages(array('name' => 'book_page', 'selected' => @$_REQUEST['book_page'], 'show_option_none' => $selectpage)); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('ASIN', 'author-showcase'); ?></th>
                <td><input type="text" name="asin" value="<?php echo stripslashes(@$_REQUEST['asin']); ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('ISBN', 'author-showcase'); ?></th>
                <td><input type="text" name="isbn" value="<?php echo stripslashes(@$_REQUEST['isbn']); ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Cover Image', 'author-showcase'); ?>
                    <br/><small><?php echo __('(The book title is used as alt text on the image)', 'author-showcase'); ?></small>
                </th>
                <td><label for="upload_image">
                        <input id="cover_image" type="hidden" name="cover" value="<?php echo @$_REQUEST['cover']; ?>"/>
                        <button id="cover_image_button"
                                class="button img_upload"><?php echo __('Upload Image', 'author-showcase'); ?></button>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo __('Sales Links', 'author-showcase'); ?>
                    <br/><small><?php echo __('(Click add to create more)', 'author-showcase'); ?></small></th>
            </tr>
            <tr>
                <td colspan="2">
                    <table id="btbe_services_table" class="btbe_services">
                        <tr>
                            <th><?php echo __('Name', 'author-showcase'); ?></th>
                            <th><?php echo __('Icon', 'author-showcase'); ?></th>
                            <th><?php echo __('Link', 'author-showcase'); ?></th>
                            <th>
                                <button onclick="btbe_addservice(event);"><?php echo __('Add', 'author-showcase'); ?></button>
                            </th>
                        </tr>
                        <?php if (isset($services)) : ?>
                            <?php foreach ($services as $idx => $s) : ?>
                                <tr id="row<?php echo $idx; ?>">
                                    <td><input name="sername<?php echo $idx; ?>" type="text"
                                               value="<?php echo $s['name']; ?>"/></td>
                                    <td><input type="hidden" id="sericon<?php echo $idx; ?>" class="img_upload"
                                               name="sericon<?php echo $idx; ?>" value="<?php echo $s['icon']; ?>"/>
                                        <button class="img_upload"
                                                id="sericon<?php echo $idx; ?>_button"><?php echo __('Upload Icon', 'author-showcase'); ?></button>
                                        <img src="<?php echo $s['icon']; ?>" height="32" width="32"/></td>
                                    <td><input type="text" name="serlink<?php echo $idx; ?>"
                                               value="<?php echo $s['link']; ?>"/></td>
                                    <td>
                                        <button onclick="btbe_remove(event, 'row<?php echo $idx; ?>');"><?php echo __('Remove', 'author-showcase'); ?></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="submit">
                        <input type="hidden" name="btbe_add" value="1"/>
                        <input type="submit" class="button-primary" value="<?php echo __('Save', 'author-showcase'); ?>"
                               onclick="btbe_checktitle(event)"/>
                    </p>
                </td>
                <td><p class="submit"><a href="<?php echo admin_url('admin.php?page=btbe_admin'); ?>"
                                         class="button"><?php echo __('Cancel', 'author-showcase'); ?></a></p></td>
            </tr>
            <?php if (@$_REQUEST['btbe_id'] > 0) : ?>
                <tr>
                    <td><p class="submit"><a
                                    href="<?php echo admin_url('admin.php?page=btbe_add&btbe_id=' . $_REQUEST['btbe_id'] . '&delete=1'); ?>"><?php echo __('Delete this book', 'author-showcase'); ?></a>
                        </p></td>
                </tr>
            <?php endif; ?>
        </table>
        <div id="btbe_cover"><?php if (@$_REQUEST['cover'] != "") : ?><img
                src="<?php echo @$_REQUEST['cover']; ?>" /><?php endif; ?></div>
        <div class="clear"></div>
    </div>
</form>