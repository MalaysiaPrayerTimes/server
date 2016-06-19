$(document).ready(function()  {
  window.prettyPrint && prettyPrint();

  $(".sidenav").affix({
    offset: {
      top: function () { return $(window).width() <= 980 ? 80 : 0 },
      bottom: 150
    }
  });

  var options = {
    "server": "/mpt.json"
  };

  $("#mpte, #mptf, #mptg").mpt(options);
  $("#mpte").bind("prayerChanged", function(e) {
    var prayerNames = ["Subuh", "Syuruk", "Zohor", "Asar", "Maghrib", "Isyak"];
    var time = e.currentPrayerTime.getHours() + ":" + e.currentPrayerTime.getMinutes();
    $("#mpte .mpt-prayer").html(prayerNames[e.currentPrayer] + " (" + time + ")");
  });

  $("#mpte, #mptf, #mptg").mpt("getData", "kdh-1");
  
});