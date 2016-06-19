var geocoder;
var filter = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Malaysia'];
var prayerNames = ["Subuh", "Syuruk", "Zohor", "Asar", "Maghrib", "Isyak"];
var detectedLocation = [];
var loc_time;

function createReadableTime(d) {
  var h = d.getHours();
  var m = d.getMinutes();
  return (h  < 10  ? "0" + h : h)  + ":" + (m  < 10  ? "0" + m : m);
}

function gl_ok(pos) {   
  console.log("ok: " + pos);
  clearTimeout(loc_time);
  $('#mpt-cus-loc').attr('placeholder', 'Resolving location...');
  codeLatLng(pos.coords.latitude, pos.coords.longitude);
}

function gl_er(msg) {
  console.log("er: " + msg);
  clearTimeout(loc_time);
  $('#mpt-cus-loc').attr('placeholder', 'Enter location...');
  showLoadingButton(false);
}

function gc_ok(code) { 
  if (code != -1) {
    $('#mpt').mpt('getData', code);
  } else {
    gl_er();
  }
  
}

function mpt_onError(e) {
  $('#mpt-cus-loc').val('');
  $('.control-group').addClass('error');
  showLoadingButton(false);
  $('.alert').fadeIn();
  
  switch (e.errorType) {
    case 'ERROR_NO_PLACE':
      $('.errmsg').html("Location unavailable. Please choose another location.");
      break;
    case 'ERROR_ESOLAT':
      $('.errmsg').html("Unable to connect to JAKIM's server. Please try again later.");
      break;
    default:
      $('.errmsg').html("An unexpected error has occured. Please try again later.");
      break;
  }
  
}

function mpt_onPrayerChanged(e) {
  $('#mpt .mpt-prayer-name').html(prayerNames[e.nextPrayer]);
  $('#mpt .mpt-prayer-time').html(createReadableTime(e.nextPrayerTime));
  $('.mpt-top').slideDown();
  $('.mpt-mid').slideDown();
  $('.mpt-bot').slideUp();
  $('.mpt-change-loc').slideDown();
  showLoadingButton(false);
}

function gc_er(err) {
  $('#mpt-cus-loc').attr('placeholder', 'Enter location...');
  showLoadingButton(false);
}

function findCode(results, status) {
  var query = [];
  var area = -1;
  var region = "";

  console.log(results);
  console.log(status);

  if (status == google.maps.GeocoderStatus.OK) {
    if (results[1]) {
      for (i = 0; i < results.length; i++) {
        acs = results[i].address_components;
        for (j = 0; j < acs.length; j++) {
          pcs = acs[j].long_name;
          if ($.inArray(pcs, filter) == -1 && $.inArray(pcs, query) == -1) {
            if (pcs.match("[0-9]+") == null) {
              query.push(pcs);
            }
          }
        }
      }
      
      detectedLocation = query;
      console.log(query);

      if ($.inArray("Penang", query) != -1) {
        area = "png-1";
        region = "png";
        code = 1;
      }

      ksd: for (var key in statedetails) {
        for (k = 0; k < query.length; k++) {
          code = $.inArray(query[k], statedetails[key]);

          if (code != -1) {
            area = key + "-" + code;
            region = key;
            break ksd;
          }
        }
      }
      gc_ok(area);
    } else {
      gc_er(-1);
    }
  } else {
    gc_er(-2);
  }
}

function codeLatLng(lat, lng) {
  console.log('latlng: ' + lat + " " + lng);
  var latlng = new google.maps.LatLng(lat, lng);
  geocoder = new google.maps.Geocoder();
  geocoder.geocode({'latLng': latlng}, findCode);
}

function changeLocation() {

  if (navigator.geolocation) {
    $('.mpt-loc-ad').show();
  } else {
    $('.mpt-loc-ad').hide();
  }

  $('#mpt-cus-loc').attr('placeholder', 'Enter location...');
  $('.mpt-top').slideUp();
  $('.mpt-mid').slideUp();
  $('.mpt-bot').slideDown(function() {
    $('#mpt-cus-loc').focus();
  });

  //$('.mpt-loc-ca').show();
  $('.mpt-loc-ca').click(function() {
    $('.mpt-top').slideDown();
    $('.mpt-mid').slideDown();
    $('.mpt-bot').slideUp();
    showLoadingButton(false);
    $('.alert').fadeOut();
    $('.control-group').removeClass('error');
  });

}

function setLocation() {  
  var area = $('#mpt-cus-loc').val();
  showLoadingButton(true);
  $('#mpt').mpt('getData', {area: area, location:detectedLocation});
  $('.control-group').removeClass('error');
  $('.alert').fadeOut();
}

function showLoadingButton(show) {
  if (!show) {
    $('.mpt-loc-ok > i').addClass('icon-ok');
    $('.mpt-loc-ok > i').removeClass('icon-download-alt');
    $('#mpt-cus-loc, .mpt-loc-ok').attr('disabled', null);
  } else {
    $('.mpt-loc-ok > i').removeClass('icon-ok');
    $('.mpt-loc-ok > i').addClass('icon-download-alt');
    $('#mpt-cus-loc, .mpt-loc-ok').attr('disabled', 'disabled');
  }
}

function toggleTable() {
  var tg = $('.mpt-tab > i').hasClass("icon-plus-sign");
  var tb = $('.mpt-table');

  if (tg) {
    tb.slideDown(function() {
      $('.mpt-tab > i').removeClass("icon-plus-sign");
      $('.mpt-tab > i').addClass("icon-minus-sign");
    });
  } else {
    tb.slideUp(function() {
      $('.mpt-tab > i').addClass("icon-plus-sign");
      $('.mpt-tab > i').removeClass("icon-minus-sign");
    });
  }
  
}

function geolocFail() {
  clearTimeout(loc_time);
  gl_er();
}

function createShareButtons() {
  $('.twli').append('<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://mpt.i906.my/">Tweet</a>');
  $('.fbli').append('<div class="fb-like" data-href="https://www.facebook.com/pages/Malaysia-Prayer-Times/369813589710705" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false"></div>');
  $('.gpli').append('<div class="g-plusone" data-size="medium" data-href="http://mpt.i906.my/"></div>');
}

$(document).ready(function()  {

  var locations = [];

  for (var key in statedetails) {
    for (var k = 0; k < statedetails[key].length; k++) {
      locations.push(statedetails[key][k])
    }
  }

  $('#mpt-cus-loc').typeahead({
    source: locations,
    items: 4,
    minLength: 1
  });

  $(".mpt-change-loc").click(changeLocation);
  $(".mpt-tab").click(toggleTable);
  $(".mpt-loc-ok").click(setLocation);
  $("#mpt-cus-loc").keypress(function(e) {
    if (e.keyCode == 13) {
      setLocation();
    }
  });

  var options = {
    'server': '/mpt.json'
    //'showNextPrayer': true,
    //'timeFormat': createReadableTime
  };

  $('#mpt').mpt(options);
  $('#mpt').bind('prayerChanged', mpt_onPrayerChanged);   
  $('#mpt').bind('error', mpt_onError);

  if (navigator.geolocation) {
    $('#mpt-cus-loc').attr('placeholder', 'Retrieving location...');
    $('.alert > button').click(function() {
      $('.alert').fadeOut();
    });

    loc_time = setTimeout("geolocFail()", 30000);
    navigator.geolocation.getCurrentPosition(gl_ok, gl_er, {timeout:30000});
    showLoadingButton(true);
  } else {
    geolocFail();
  }

  $('.carousel').carousel();

  createShareButtons();
});
