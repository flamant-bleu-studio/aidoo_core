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

<div class="feed_rss" id="rss_{$id}">
<img id="rss_loader_{$id}" src="{$baseUrl}{$skinUrl}/img/loader.gif" />
</div>

{literal}
<script type="text/javascript">
$(document).ready(function() {	
	$.ajaxCMS({
		url : "/admin/bloc/rss", 
		datas : {
			id_bloc : {/literal}{$id}{literal} 
		},
		success : function(e){
			$('#rss_loader_{/literal}{$id}{literal}').remove();

			if (e['error'] == false)
				$("#rss_{/literal}{$id}{literal}").append(e['rss']);
			else
				$("#rss_{/literal}{$id}{literal}").append('Flux momentanément indisponible.');
		},
		error : function(){}
	});
});

</script>
{/literal}
