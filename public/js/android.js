function createShareButtons() {
  $('.twli').append('<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://mpt.i906.my/">Tweet</a>');
  $('.fbli').append('<div class="fb-like" data-href="https://www.facebook.com/pages/Malaysia-Prayer-Times/369813589710705" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false"></div>');
  $('.gpli').append('<div class="g-plusone" data-size="medium" data-href="http://mpt.i906.my/"></div>');
}

$(document).ready(function()  {
  createShareButtons();
});