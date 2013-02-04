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

{appendFile type="css" src=$smarty.const.COMMON_LIB_PATH|cat:"/lib/fancybox/fancybox.css"}
{appendFile type="js" src=$smarty.const.COMMON_LIB_PATH|cat:"/lib/fancybox/jquery.fancybox-1.3.4.pack.js"}

<div style="display: none;">
	
	<div id="bloc_popup_{$id}">
		
		<div class="content">
		
			{$text}
		
		</div>
		
	</div>
	
</div>

<script type="text/javascript">

$(document).ready(function(){
	
	$.fancybox({
		'autoDimensions': true,
		'scrolling'		: 'auto',
		'width'			: false,
		'height'		: false,
		'titleShow'		: false,
		'autoScale'		: true,
		'type'			: 'inline',
		'href' 			: '#bloc_popup_{$id}'
		{if displayOnce},
			'onClosed'		: function(){
				var today = new Date(), expires = new Date();
				
				{if $timeValid == 0}
			        expires.setTime(today.getTime() + (365*24*60*60*1000));
			    {else}
			    	expires.setTime(today.getTime() + ({$timeValid}*1000));
			    {/if}
				
			    document.cookie = "{$nameCookie}=true;expires=" + expires.toGMTString();
			}
		{/if}
	});
	
});

</script>
