var geocoder;

var filter = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Malaysia'];


function codeLatLng(lat, lng) {
	var latlng = new google.maps.LatLng(lat, lng);
	var query = [];
	var area = -1;
	var region = "";

	geocoder = new google.maps.Geocoder();

	geocoder.geocode({'latLng': latlng}, function(results, status) {
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
											console.log(area);
				return area;
			} else {
				return -1;
			}
		} else {
			return -2;
		}
	});
}


