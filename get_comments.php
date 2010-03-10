<?php
if (isset($_GET['post']) && isset($_GET['stream']) && isset($_GET['cpage'])) {
  $post_ID = (int)$_GET['post'];
  $n = (int)$_GET['stream'];
  $cpage = (int)$_GET['cpage'];
  require(dirname(__FILE__).'/../../../wp-load.php');
  query_posts(array('p'=>$post_ID, 'post_type'=>'any'));
  if (!have_posts())
    return;
  global $commentplus;
  the_post();

  if(!$commentplus->has_streams($n))
    return;
  set_query_var('cpage', $cpage);
  $commentplus->init_ajah();
  comments_template('/commentplus_ajah');
}
?>
