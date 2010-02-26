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

  var cpage = parseInt($('meta[name="cpage"]').attr('content'));
  if (cpage < 1)
    cpage = 1;
  $('.commentplus_ajah').each(function(n){
    load(this, n, cpage);
  });
});
function load(thus, stream, cpage) {
  jQuery(function($){
    var get_comments_url = $('link[rel="commentplus_ajah"]').attr('href');
    var post = $('input[name="comment_post_ID"]').attr('value');
    $(thus).slideUp();
    $(thus).load(get_comments_url, 'post='+post+'&stream='+stream+'&cpage='+cpage, function(){$(this).slideDown();fiddle_comments_links(thus, stream, cpage)});
  });
}
function fiddle_comments_links(thus, n, cpage) {
  jQuery(function($){
    $(thus).find('.next_comments_link').click(function(){
      load(thus, n, cpage+1);
      return false;
    });
    $(thus).find('.previous_comments_link').click(function(){
      load(thus, n, cpage-1);
      return false;
    });
  });
}
