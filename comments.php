<?php
$streams = json_decode(get_post_meta($post->ID, 'commentplus',1));

if (comments_open() || have_comments()) {
  if ($streams) {
    foreach($streams as $stream) {
      echo "<p>$stream</p>\n";
    }
  }
} else {
  echo '<p class="nocomments">Comments are closed.</p>';
}

?>
