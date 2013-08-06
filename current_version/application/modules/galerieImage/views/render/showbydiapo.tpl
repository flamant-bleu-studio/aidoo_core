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

{function name=addControls}
	{if $galerie->controls_style == "0"}
		<div id="gallerie_action">
			<input id="previous_gallerie" type="image" src="{$baseUrl}{$skinUrl}/images/slideshow/prev.png" value="" /> 
			<input id="start_gallerie" type="image" src="{$baseUrl}{$skinUrl}/images/slideshow/play.png" value="" {if $galerie->autostart}style="display:none;"{/if} />
			<input id="stop_gallerie" type="image" src="{$baseUrl}{$skinUrl}/images/slideshow/pause.png" value="" {if !$galerie->autostart}style="display:none;"{/if} /> 
			<input id="next_gallerie" type="image" src="{$baseUrl}{$skinUrl}/images/slideshow/next.png" value="" />
		</div>
	{else if $galerie->controls_style == "1"}
		<div id="gallerie_thumbs"><ul>
			{foreach from=$galerie->nodes key=key item=image}
				<li>
					<a href="#"><img src="{$image->path_thumb}" style="width:50px;"/></a>
				</li>
			{/foreach}
		</ul>
		<div class="clear"></div>
		</div>
	{/if}
{/function}


{if $galerie->transition == "fade"}
	{appendFile src="/lib/slideShow/jquery.slideShowMaison.js" type="js"}
{else if $galerie->transition == "slide"}
	{appendFile type="css" src="{$smarty.const.COMMON_LIB_PATH}/lib/bxslider/jquery.bxslider.css"}
	{appendFile type="js" src="{$smarty.const.COMMON_LIB_PATH}/lib/bxslider/jquery.bxslider.min.js"}
{/if}

{if $galerie->nodes[1] && $galerie->controls_position == "top"}
	{call name=addControls}
{/if}

<div class="liste_photo" id="diapo_photo">

{if $galerie->transition == "fade"}

	{foreach from=$galerie->nodes key=key item=image}
		<div class="image">
			<img src="{$image->path}" data-color="#{$image->bg_color}"/>
			<div class="description">{$image->description}</div>
		</div>
	{/foreach}

{else if $galerie->transition == "slide"}

		<ul id="galerieFront">
		{foreach from=$galerie->nodes key=key item=image}
			<li style="width:{$maxWidth}px;">
				<img src="{$image->path}" data-color="#{$image->bg_color}" />
				<div class="description">{$image->description}</div>
			</li>
		{/foreach}
		</ul>
	
{/if}

</div>

{if $galerie->nodes[1] && $galerie->controls_position == "bottom"}
	{call name=addControls autostart=$galerie->autostart}
{/if}

{appendScript type="css"}
body {
background-color: #{$galerie->nodes[0]->bg_color};
}

#galerieFront {
margin: 0;
padding: 0;
}

#gallerie_thumbs {
text-align: center;
}

#gallerie_thumbs li {
list-style: none;
display: inline-block;
}

#gallerie_thumbs li a {
display: block;
}

#gallerie_thumbs li a.pager-active {
border: 1px solid red;
}

{/appendScript}

<script type="text/javascript">

$(document).ready(function() {

	var maxHeight = "{$maxHeight}";
	var maxWidth = "{$maxWidth}";

	function changeColor(color){
		if(!color || color == "#")
			color = "#fff";
		
		var body = $("body").css({
			"background-color": color
		});
		
		if(color == "#000" || color == "#000000")
			var textColor = "#fff";
		else
			var textColor = "#000";
		
		$("#header .menuitems a").css("color", textColor);
	}
	
	// Affichage de la page avec la couleur de fond de la première photo
	changeColor($('#gallerie_photo').find("div:first .bg_photo").text());
	
{if $galerie->transition == "fade"}

	if($('#gallerie_photo').children().length > 1){
		
		$('#gallerie_photo').gallery({
			type: "fade",
			startStopBtnId : $("#startstop_gallerie"),
			autoStart: {if $galerie->autostart}true{else}false{/if},
			delay: 2000,
			onNext: function(elem){
				changeColor(elem.find("img").data("color"));
			},
			onPrev: function(elem){
				changeColor(elem.find("img").data("color"));
			}
		});

		$('#gallerie_thumbs a:first').addClass('pager-active');
		$('#gallerie_thumbs a').on("click", function(e){
			e.preventDefault();
			
		   	var thumbIndex = $('#gallerie_thumbs a').index(this);
		   	$('#gallerie_photo').data("gallery").goTo(thumbIndex);

		    $('#gallerie_thumbs a').removeClass('pager-active');
			$(this).addClass('pager-active');

		});
		
		$("#start_gallerie").on('click', function(){
			$('#gallerie_photo').data("gallery").start();
			$("#stop_gallerie").show();
			$("#start_gallerie").hide();
		});
		$("#stop_gallerie").on('click', function(){
			$('#gallerie_photo').data("gallery").stop();
			$("#stop_gallerie").hide();
			$("#start_gallerie").show();
		});
		$("#previous_gallerie").on('click', function(){
			$('#gallerie_photo').data("gallery").previous();
		});
		$("#next_gallerie").on('click', function(){
			$('#gallerie_photo').data("gallery").next();
		});
	}

{else if $galerie->transition == "slide"}
	var bxslider = $("#galerieFront").bxSlider({
		auto: true,
		autoStart: {if $galerie->autostart}true{else}false{/if},
		
		controls: false,
		
		displaySlideQty: 1,
		moveSlideQty: 1,
		
		prevText: '',
		nextText: '',
		infiniteLoop: true
	});

	$('#gallerie_thumbs a:first').addClass('pager-active');
	$('#gallerie_thumbs a').on("click", function(e){
		e.preventDefault();
		
	   	var thumbIndex = $('#gallerie_thumbs a').index(this);
	   	bxslider.goToSlide(thumbIndex);

	    $('#gallerie_thumbs a').removeClass('pager-active');
		$(this).addClass('pager-active');

	});
	  
	$("#start_gallerie").on('click', function(){
		bxslider.startShow();
		$("#stop_gallerie").show();
		$("#start_gallerie").hide();
	});
	$("#stop_gallerie").on('click', function(){
		bxslider.stopShow();
		$("#stop_gallerie").hide();
		$("#start_gallerie").show();
	});
	$("#previous_gallerie").on('click', function(){
		bxslider.goToPreviousSlide();
	});
	$("#next_gallerie").on('click', function(){
		bxslider.goToNextSlide();
	});
{/if}
});
</script>
