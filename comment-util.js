jQuery(function($){
  // Make streams collapse
    if (false)
  $('.commentplus_stream').each(function(){
    var stream = $(this);
    stream.find('.commenting').hide();
    stream.find('h4').click(function(){
      $(this).parent('.commentplus_stream').find('.commenting').slideToggle('slow');
    });
  });

  // Remove .navigation and add our AJAH
  $('.navigation').remove();
  $('.commentlist').remove();
  $('.commenting').append('<div class="commentplus_ajah"></div>');

  $('.commentplus_ajah').each(function(n){
    load(this, n);
  });
});
function load(thus, stream, cpage) {
  jQuery(function($){
    var get_comments_url = $('link[rel="commentplus_ajah"]').attr('href');
    var post = $('input[name="comment_post_ID"]').attr('value');
    if (cpage == null)
      cpage = $('meta[name="cpage"]').attr('content');
    $(thus).load(get_comments_url, 'post='+post+'&stream='+stream+'&cpage='+cpage, function(){fiddle_comments_links(thus,stream)});
  });
}
function fiddle_comments_links(commenting, n) {
  jQuery(function($){
    $(commenting).find('.next_comments_link').click(function(){
      load(commenting, n, 2);
      return false;
    });
  });
}
