/**
 * jQuery.MPT
 * Written by Noorzaini Ilhami (i906)
 * Malaysia Prayer Times
 *
 * @author Noorzaini Ilhami
 * @version 1.0
 * 
 **/

!function ($) {

  "use strict"; // jshint ;_;

  var MPT = function (element, options) {
    this.$elx = element;
    this.$element = $(element);
    this.options = $.extend({}, $.fn.mpt.defaults, options);

    //console.log("MPT")
    var $this = this;  
  }

  MPT.prototype = {

    updatePrayerTime: function() {
      var $this = this;
      var $el = this.$element;
      var gpt = this.getPrayerTime();
      var npt = this.getNextPrayerTime();
      var gsl = this.getSecondsLeft(npt) + 1000;

      this.populatePrayerTable();
      //console.log("[mpt] Updating in " + Math.floor(gsl / 1000 / 60) + " minutes.");

      var e = $.Event("prayerChanged", {
        currentPrayer: this.getCurrentPrayer(),
        nextPrayer: this.getNextPrayer(),
        currentPrayerTime: gpt,
        nextPrayerTime: npt
      });

      $el.trigger(e);

      setTimeout(function () {
        $this.updatePrayerTime();
      }, gsl);
    },

    getData: function(args) {
      var $this = this;
      var sug = [];
      var loccode = "";

      if (typeof args == 'object') {
        loccode = args.area;
        sug = args.location;
      } else {
        loccode = args;
      }

      if (loccode == undefined) {
        $.error('No location code specified.');
      }

      $.ajax(this.options.server, {
        dataType: 'json',
        data: {
          code: loccode,
          location: sug,
          appid: 'mpt-jquery',
          appurl: 'http://' + window.location.hostname
        },

        error: function(jqXHR, textStatus, errorThrown) {

          var jsonValue = jQuery.parseJSON(jqXHR.responseText);
          var e;

          if (jsonValue != null) {
            e = $.Event("error", {
              errorDetail: jsonValue.meta.errorDetail,
              errorType: jsonValue.meta.errorType
            });
          } else {
            e = $.Event("error", errorThrown);            
          }

          $this.$element.trigger(e);
        },

        success: function(data, textStatus, jqXHR) {
          //console.log(data);

          if (data.meta.code == 200) {
            var cmpp = $this.$element.find('.mpt-prayer-place');
            cmpp.html(data.response.place);
            $.data($this.$elx, "mptsr", data.response);
            $this.updatePrayerTime();
          } else {
            var e = $.Event("error", {
              errorDetail: data.meta.errorDetail,
              errorType: data.meta.errorType
            });

            $this.$element.trigger(e);
          }
        }
      });
    },

    reportUnknownLocation: function(args) {
      var $this = this;

      $.ajax(this.options.report, {
        data: {
          add: args,
          cde: "mpt-jquery"
        },
        headers: {
          "X-Requested-From": "mpt-jquery"
        }
      });
    },

    populatePrayerTable: function() {
      var cp = this.getCurrentPrayer();

      for (var t = 0; t < 6; t++) {
        var p1 = this.$element.find('.mpt-prayer-' + t + ' > .time');
        var p2 = this.$element.find('.mpt-prayer-' + t + '-time');
        var p3 = this.$element.find('.mpt-prayer-' + t + '-name');

        if (cp == t) {
          p1.parent().addClass("active");
          p2.addClass("active");
          p3.addClass("active");
        } else {
          p1.parent().removeClass("active");
          p2.removeClass("active");
          p3.removeClass("active");
        }

        p1.html(this.options.timeFormat.call(undefined, this.getPrayerTime(t)));
        p2.html(this.options.timeFormat.call(undefined, this.getPrayerTime(t)));
      }
    },

    getPrayerTime: function(index) {
      var rawdata = this.$element.data("mptsr");
      var now = new Date();
      //var yes = new Date(now.getTime() - 86400000);
      var cp = (index != undefined) ? index : this.getCurrentPrayer();
      var d1 = new Date(rawdata.times[now.getDate() - 1][cp] * 1000);
      //var d2 = new Date(rawdata.times[yes.getDate() - 1][cp] * 1000);
      //return (d1 > now) ? d2 : d1;
      return d1;
    },

    getNextPrayerTime: function() {
      var rawdata = this.$element.data("mptsr");
      var now = new Date();
      var tom = new Date(now.getTime() + 86400000);
      var np = this.getNextPrayer();
      var e1 = new Date(rawdata.times[now.getDate() - 1][np] * 1000);
      var e2 = new Date(rawdata.times[tom.getDate() - 1][np] * 1000);
      return (e1 < now) ? e2 : e1;
    },

    getCurrentPrayer: function() {
      var rawdata = this.$element.data("mptsr");
      var now = new Date();
      var data = rawdata.times[now.getDate() - 1];
      var pos = 0;

      for (var i = 0; i < data.length; i++) {
        var currentTime = new Date(data[i] * 1000);

        if (currentTime > now) {
          break;
        }
        pos++;
      }
      return (pos - 1 == -1) ? 5 : pos - 1;
    },

    getNextPrayer: function() {
      return (this.getCurrentPrayer() + 1) % 6;
    },

    getSecondsLeft: function(time) {
      var dateStart = new Date();
      var dateEnd = new Date(time);
      return (dateEnd - dateStart);
    }
  };

  $.fn.mpt = function (option, arg1) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data('mpt');
      var options = typeof option == 'object' && option;
      var action = typeof option == 'string';

      if (!data) $this.data('mpt', (data = new MPT(this, options)));
      if (action) {
        if (option == 'getData') {
          var z = data[option](arg1);
          return z;
        } else if (option == 'reportUnknownLocation') {
          data[option](arg1);
        }
      }
    });
  }

  $.fn.mpt.defaults = {
    server: 'http://mpt.i906.my/mpt.json',
    report: 'http://mpt.i906.my/location.php',
    timeFormat: function (d) {
      var h = d.getHours();
      var m = d.getMinutes();
      return (h  < 10  ? "0" + h : h)  + ":" + (m  < 10  ? "0" + m : m);
    }
  }

  $.fn.mpt.Constructor = MPT;

}(window.jQuery);