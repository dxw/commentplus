<?php
if (isset($_GET['post']) && isset($_GET['stream']) && isset($_GET['cpage'])) {
  $post_ID = (int)$_GET['post'];
  $n = ((int)$_GET['stream']) - 1;
  $cpage = (int)$_GET['cpage'];
}
require(dirname(__FILE__).'/../../../wp-load.php');
query_posts(array('p'=>$post_ID));
if (!have_posts())
  return;
the_post();
//require(dirname(__FILE__).'/commentplus.php');

$streams = json_decode(get_post_meta($post->ID, '_commentplus',1));
if (!isset($streams[$n]))
  return;
$stream = $streams[$n];
set_query_var('cpage', $cpage);
comments_template('/commentplus_ajah');
?>
