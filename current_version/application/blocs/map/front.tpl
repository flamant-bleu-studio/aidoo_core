{*
* CMS Aïdoo
* 
* Copyright (C) 2013  Flamant Bleu Studio
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
* Lesser General Public License for more details.
* 
* You should have received a copy of the GNU Lesser General Public
* License along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
*}

{if $datas.service == "googlemap"}
	<script type="text/javascript">
		function initialize{$id}() {
			var myOptions = {
				center: new google.maps.LatLng({$datas.latitude}, {$datas.longitude}),
				zoom: {$datas.zoom},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("googleMap-{$id}"), myOptions);
	
			var companyPos = new google.maps.LatLng({$datas.latitude}, {$datas.longitude});
			var companyMarker = new google.maps.Marker({
				position: companyPos,
				map: map
			});
		}
		
		function loadScript() {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://maps.googleapis.com/maps/api/js?key={$datas.apiKey}&sensor=false&callback=initialize{$id}";
			document.body.appendChild(script);
		}
	
		window.onload = loadScript;
	      
		
		var map = new L.Map('map');
		map.setView(new L.LatLng({$datas.latitude}, {$datas.longitude}), 13).addLayer(cloudmade);
	
		var marker = new L.Marker(new L.LatLng({$datas.latitude}, {$datas.longitude}));
		map.addLayer(marker);
	
		marker.bindPopup('A pretty CSS3 popup.<br />Easily customizable.').openPopup();
		
	</script>
	
	<div id="googleMap-{$id}" style="width:{$datas.mapWidth}px; height:{$datas.mapHeight}px;"></div>
	
{else if $datas.service == "mapquest"}
	<script src="http://www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key={$datas.apiKey}"></script>
	
	<script type="text/javascript"> 
    MQA.EventUtil.observe(window, 'load', function() {
	
		var options = {
			elt: document.getElementById('mapQuest-{$id}'),       /*ID of element on the page where you want the map added*/ 
			zoom: {$datas.zoom},                                  /*initial zoom level of the map*/ 
			latLng: {
				lat:{$datas.latitude}, 
				lng:{$datas.longitude}
			},  /*center of map in latitude/longitude */ 
			mtype: 'map',                              /*map type (map)*/ 
			bestFitMargin: 0,                          /*margin offset from the map viewport when applying a bestfit on shapes*/ 
			zoomOnDoubleClick: true                    /*zoom in when double-clicking on map*/ 
		};
	
		/*Construct an instance of MQA.TileMap with the options object*/ 
		window.map = new MQA.TileMap(options);
		
		/* Marker */
		var basic = new MQA.Poi({
			lat: {$datas.latitude}, 
			lng: {$datas.longitude}
		});
		map.addShape(basic);
		
		/* Controls */
		MQA.withModule('smallzoom', function() {
			map.addControl(
				new MQA.SmallZoom(),
				new MQA.MapCornerPlacement(MQA.MapCorner.TOP_LEFT, new MQA.Size(5,5))
			);
		});
		
		/* Types de vues */
		MQA.withModule('viewoptions', function() {
			map.addControl(
				new MQA.ViewOptions()
			);
		});


	});
	  </script> 
  
	<div id="mapQuest-{$id}" style="width:{$datas.mapWidth}px; height:{$datas.mapHeight}px;"></div>
	
	{if $datas.getDirections}
		<div class='get-directions'>
			<a href='http://www.mapquest.com/?le=t&daddr={$smarty.const.BLOC_MAP_LATITUDE} {$smarty.const.BLOC_MAP_LONGITUDE}&vs=directions&geocode=LATLNG' class='fancybox'>
				{t}Get directions{/t}
			</a>
	  	</div>
  	{/if}
{/if}
