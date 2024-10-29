<?php

function btbe_generateWidgetDisplay($book_array, $showfields, $idstring, $icons_visible = false) {
  if(strpos($idstring, ',') !== false) {
    $book_array = btbe_reorderBooks($book_array, $idstring);
  }
  $boxwidth = (100/count($book_array))-2;
  $html = '<div class="btbe_row">';
  $boxwrap = '<div class="btbe_box" style="width:'.$boxwidth.'%;">';
  $before_image='<div class="toggle"><a class="trigger" href="#"><img class="btbalign" style="width: 100%; height: auto;"';
	$after_image='" / ></a><div class="box box-invisible">';
  $after_services="</div></div>";
  $endbox="</div>";
  if($icons_visible) {
    $before_image='<div class="toggle"><img class="btbalign" style="width: 100%; height: auto;"';
    $after_image='" / ></a><div class="box box-visible">';
  }
  
  foreach ($book_array as $book) {
    $services = json_decode($book['services'], true);
    $html .= $boxwrap.$before_image.' src="'.esc_url($book['cover']).'" alt="'.stripslashes($book['title']).'"'.$after_image;
    foreach ($services as $s) {
      $html .= '<a href="' . esc_url($s['link']) . '" title="' . stripslashes($s['name']) . '" target="_blank"><img src="' . esc_url($s['icon']) . '" alt="' . stripslashes($s['name']) . '" width="32" height="32" /></a>';
    }
    $html .= $after_services;
    if(in_array('title', $showfields)) {
      $html .= '<h3>'.stripslashes($book['title']).'</h3>';
    }
    if(in_array('subtitle', $showfields)) {
      $html .= '<h4>'.stripslashes($book['subtitle']).'</h4>';
    }
    if(in_array('series', $showfields)) {
      $html .= '<h4>'.stripslashes($book['series']).' '.stripslashes($book['series_num']).'</h4>';
    }
    if(in_array('author', $showfields)) {
      $html .= '<h4>'.stripslashes($book['author']).'</h4>';
    }
    if(in_array('short_blurb', $showfields)) {
      $html .= '<p>'.stripslashes($book['short_blurb']).'</p>';
    }
    if($book['book_page'] != '') {
      $html .= '<div style="clear: both";><a href="'.get_page_link($book['book_page']).'"><button class="btbe_button">Find out more</button></a></div>';
    }
    $html .= $endbox;
  }
  return $html.'</div>';
}

function btbe_generateRandomWidgetDisplay($book, $showfields, $icons_visible = false) {
  $html = "";
  $boxwrap = '<div class="btbe_box" style="padding: 2%; width:96%; float: left;">';
  $before_image='<div class="toggle"><a class="trigger" href="#"><img class="btbalign" style="width: 100%; height: auto;"';
	$after_image='" / ></a><div class="box box-invisible">';
  $after_services="</div></div>";
  $endbox="</div>";
  if($icons_visible) {
    $before_image='<div class="toggle"><img class="btbalign" style="width: 100%; height: auto;"';
    $after_image='" / ></a><div class="box box-visible">';
  }
  $services = json_decode($book['services'], true);
  $html .= $boxwrap.$before_image.' src="'.esc_url($book['cover']).'" alt="'.stripslashes($book['title']).'"'.$after_image;
  foreach ($services as $s) {
    $html .= '<a href="' . esc_url($s['link']) . '" title="' . stripslashes($s['name']) . '" target="_blank"><img src="' . esc_url($s['icon']) . '" alt="' . stripslashes($s['name']) . '" width="32" height="32" /></a>';
  }
  $html .= $after_services;
  if(in_array('title', $showfields)) {
    $html .= '<h3>'.stripslashes($book['title']).'</h3>';
  }
  if(in_array('subtitle', $showfields)) {
    $html .= '<h4>'.stripslashes($book['subtitle']).'</h4>';
  }
  if(in_array('series', $showfields)) {
    $html .= '<h4>'.stripslashes($book['series']).' '.stripslashes($book['series_num']).'</h4>';
  }
  if(in_array('author', $showfields)) {
    $html .= '<h4>'.stripslashes($book['author']).'</h4>';
  }
  if(in_array('short_blurb', $showfields)) {
    $html .= '<p>'.stripslashes($book['short_blurb']).'</p>';
  }
  if($book['book_page'] != '') {
    $html .= '<div style="clear: both";><a href="'.get_page_link($book['book_page']).'"><button class="btbe_button">Find out more</button></a></div>';
  }
  $html .= $endbox;
  return $html;
}

function btbe_generatePageDisplay($book_array, $atts) {
  $html = "";
  if(isset($atts['books']) && strpos($atts['books'], ',') !== false) {
    $book_array = btbe_reorderBooks($book_array, $atts['books']);
  }
  if(!isset($atts['type'])) { $atts['type'] = 'list'; }
  if($atts['type'] == 'list') {
    $html .= "<div style='clear: both;'></div>";
    foreach ($book_array as $book) {
      $html .= '<div style="width:20%; float: left; margin: 0 1% 1% 0;"><img src="'.$book['cover'].'" style="width: 100%; height:auto; float: left;" />';
      if($book['book_page'] != "") {
        $html .= '<a href="'.get_page_link($book['book_page']).'"><button class="btbe_button" style="width: 100%;">Find out more</button></a>';
      }
      $html .= '</div>';
      $html .= '<h3 class="btbe_h3">'.stripslashes($book['title']).'</h3>';
      if($book['subtitle'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['subtitle']).'</h3>';
      if($book['series'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['series']).' '.stripslashes($book['series_num']).'</h3>';
      if($book['author'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['author']).'</h3>';
      if($book['short_blurb'] != "") $html .= '<p>'.stripslashes(nl2br($book['short_blurb'])).'</p>';
      if(@$atts['show_icons'] > 0) {
        $html .= btbe_addlinks($book['services'], true, $atts['show_icons']);
      }
      else {
        $html .= btbe_addlinks($book['services'], false);
      }
      $html .= '<div style="clear: both;"></div><hr />';
    }
  }
  if($atts['type'] == 'columns') {
    $boxwidth = 100/count($book_array);
    $padding = ($boxwidth/100)*2;
    $boxwrap = '<div class="btbe_page" style="padding: '.$padding.'%; width:'.($boxwidth-($padding*2)).'%; float: left;">';
    $afterbox = '</div>';
    $html .= "<div style='clear: both;'></div>";
    foreach ($book_array as $book) {
      $html .= $boxwrap;
      $html .= '<img src="'.$book['cover'].'" style="width: 100%; height:auto; float: left; padding: 2%;" />';
      if($book['short_blurb'] != "") $html .= "<p style=\"text-align: justify; padding: 2%;\">".stripslashes(nl2br($book['short_blurb']))."</p>";
      if($book['book_page'] != "") {
        $html .= '<a href="'.get_page_link($book['book_page']).'"><button class="btbe_button">Find out more</button></a>';
      }
      if(@$atts['show_icons'] > 0) {
        $html .= btbe_addlinks($book['services'], true, $atts['show_icons']);
      }
      else {
        $html .= btbe_addlinks($book['services'], false);
      }
      $html .= $afterbox;
    }
    $html .= '<div style="clear: both;"></div><hr />';
  }
  if($atts['type'] == 'grid') {
    $perRow = isset($atts['per_row']) ? $atts['per_row'] : 4;
    $html .= btbe_compileGridStyles($perRow);
    $html .= "<hr />";
    $add_breaker = false;
    if($perRow > 5 && $perRow %2 == 0) {
      $add_breaker = $perRow/2;
    }
    if($perRow > 5 && $perRow %2 != 0) {
      $add_breaker = ceil($perRow/2);  
    }
    $boxwrap = '<div class="btbe_grid buybook">';
    $afterbox = '</div>';
    $before_image='<div class="toggle"><a class="trigger" href="#"><img class="btbalign" style="width: 100%; height: auto;"';
    $after_image='" / ></a><div class="box box-invisible">';
    $after_services="</div></div>";
    if(isset($atts['icons_visible']) && $atts['icons_visible'] == true) {
      $before_image='<div class="toggle"><img class="btbalign" style="width: 100%; height: auto;"';
      $after_image='" / ></a><div class="box box-visible">';
    }
    $html .= "<div style='clear: both;'></div>";
    $after_services="</div></div>";
    $endbox="</div>";
    $count = 0;
    foreach ($book_array as $book) {
      $count++;
      $services = json_decode($book['services'], true);
      $html .= $boxwrap.$before_image.' src="'.esc_url($book['cover']).'" alt="'.stripslashes($book['title']).'"'.$after_image;
      foreach ($services as $s) {
        $html .= '<a href="' . esc_url($s['link']) . '" title="' . stripslashes($s['name']) . '" target="_blank"><img src="' . esc_url($s['icon']) . '" alt="' . stripslashes($s['name']) . '" width="32" height="32" /></a>';
      }
      $html .= $after_services;
      if($book['book_page'] != '') {
        $html .= '<div style="clear: both";><a href="'.get_page_link($book['book_page']).'"><button class="btbe_button">Find out more</button></a></div>';
      }
      $html .= $afterbox;
      if($add_breaker == $count) {
        $html .= "<div class='breaker'></div>";
      }
      if($count == $perRow) {
        $html .= '<div style="clear: both;"></div>';
        $count = 0;
      }
    }
    $html .= '<div style="clear: both;"></div><hr />';
  }
  if($atts['type'] == 'single') {
    include_once(dirname(__FILE__) . "/class_btbe_api_connections.php");
    $html .= "<div style='clear: both;'></div>";
    foreach ($book_array as $book) {
      $html .= '<img src="'.$book['cover'].'" style="width: 25%; height:auto; float: left; margin: 0 1% 1% 0;" />';
      $html .= '<h3 class="btbe_h3">'.stripslashes($book['title']).'</h3>';
      if($book['subtitle'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['subtitle']).'</h3>';
      if($book['series'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['series']).' '.stripslashes($book['series_num']).'</h3>';
      if($book['author'] != "") $html .= '<h4 class="btbe_h4">'.stripslashes($book['author']).'</h3>';
      if($book['blurb'] != "") $html .= '<p>'.stripslashes(nl2br($book['blurb'])).'</p>';
      if(@$atts['show_icons'] > 0) {
        $html .= btbe_addlinks($book['services'], true, $atts['show_icons']);
      }
      else {
        $html .= btbe_addlinks($book['services'], false);
      }
      $showreviews = false;
      if(@$book['isbn'] != '' || @$book['asin'] != '') {
        $reviewcode = '<div style="clear: both;"></div><h3>Reviews</h3>';
        $reviewcode .= btbe_compileReviewsStyles();
      }
      if(@$book['isbn'] != '') {
        global $wpdb;
        $goodreads = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bb_apis WHERE api_name = 'goodreads'", ARRAY_A);
        if(@$goodreads[0]['access_key'] != '') {
          $reviewcode .= '<div class="reviewframe">'.btbe_goodreads($book['isbn'], $goodreads[0]['access_key']).'</div>';
          $showreviews = true;
        }
      }
      if(@$book['asin'] != '') {
        global $wpdb;
        $amazon = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bb_apis WHERE api_name = 'amazon'", ARRAY_A);
        if(@$amazon[0]['access_key'] != '') {
          $amazon = $amazon[0];
          $amazon_reviews = new Btbe_Amazon($amazon['assoc'], $amazon['access_key'], $amazon['secret']);
          $reviewcode .= '<div class="reviewframe"><iframe width="100%" height="400" style="padding: 1%; background: #fff;" src="'.$amazon_reviews->getReviews($book['asin']).'"></iframe></div>';
          $showreviews = true;
        }
      }
      if($showreviews) { $html.= $reviewcode; }
      $html .= '<div style="clear: both;"></div><hr />';
    }
    
  }
  if($atts['type'] == 'icons_only') {
    return btbe_addlinks($book_array[0]['services'], true);
  }
  if($atts['type'] == 'links_only') {
    return btbe_addlinks($book_array[0]['services']);
  }
  return $html;
}

function btbe_reorderBooks($book_array, $idstring) {
  $ordered_array = array();
  $id_order = explode(',', $idstring);
  foreach ($id_order as $id) {
    foreach ($book_array as $idx => $book) {
      if($book['id'] == $id) {
        $ordered_array[] = $book;
        unset($book_array[$idx]);
      }
    }
  }
  return $ordered_array;
}

function btbe_addlinks($services, $useIcons = false, $size = 64) {
  $services = json_decode($services, true);
  if(empty($services)) {
    return '';
  }
  if($useIcons) {
    $html = '<div style="display: block; margin: auto; text-align: center;">';
  }
  else {
    $html = '<p style="text-align: center;"><strong>Buy this book on: <br />';
  }
  $compiled_array = array();
  foreach ($services as $s) {
    if($useIcons) {
      $compiled_array[] = '<a href="'.$s['link'].'" target="_blank"><img src="'.$s['icon'].'" width="'.$size.'" height="'.$size.'" style="margin: 5px;" /></a>';
    }
    else {
      $compiled_array[] = '<a href="'.$s['link'].'" target="_blank">'.$s['name'].'</a>';
    }
  }
  if($useIcons) {
    $html .= implode('', $compiled_array);
    $html .= '</div>';
  }
  else {
    $html .= implode(' | ', $compiled_array);
    $html .= '</strong></p>';
  }
  return $html;
}

function btbe_compileGridStyles($perRow) {
  $boxwidth = 100/$perRow;
  $padding = ($boxwidth/100)*2;
  $splitrow = $boxwidth*2;
  $splitrowpadding = ($splitrow/100)*2;
  $html = '<style>.btbe_grid { float: left; width: '.($boxwidth-($padding*2)).'%; padding: '.$padding.'%; } @media screen and (max-width: 1000px) { .btbe_grid { width: '.($splitrow-($splitrowpadding*2)).'%; padding: '.$splitrowpadding.'%; } .breaker { clear:both; } } </style>';
  return $html;
}

function btbe_compileReviewsStyles() {
  return '<style>.reviewframe { width: 100%; }</style>';
}