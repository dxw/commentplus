jQuery(function($){

  function load(thus, stream, cpage) {
    var getCommentsUrl = $('link[rel="commentplus_ajah"]').attr('href');
    var post = $('input[name="comment_post_ID"]').attr('value');
    $(thus).slideUp();
    $(thus).load(getCommentsUrl, 'post='+post+'&stream='+stream+'&cpage='+cpage, function(){$(this).slideDown();fiddleCommentsLinks(thus, stream, cpage)});
  }

  function fiddleCommentsLinks(thus, n, cpage) {
    $(thus).find('.next_comments_link').click(function(){
      load(thus, n, cpage+1);
      return false;
    });
    $(thus).find('.previous_comments_link').click(function(){
      load(thus, n, cpage-1);
      return false;
    });
  }

  // Make streams collapse
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
