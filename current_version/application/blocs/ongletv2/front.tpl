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

{if $onglets|@count > 0}
	<ul class="list-onglet">
		{foreach name=onglet from=$onglets key=key item=onglet}
			<li class="onglet {$onglet["css"]} {if $smarty.foreach.onglet.first && $smarty.foreach.onglet.last} single selected{elseif $smarty.foreach.onglet.first} first selected{elseif $smarty.foreach.onglet.last} last{else} onglet-{$key}{/if}">
				<span>{$onglet["title"]}</span>
			</li>
		{/foreach}
	</ul>


	<div class="list-render">
		{foreach name=listOnglet from=$onglets key=keyOnglet item=onglet}
			<div class="render {$onglet["css"]} {if $smarty.foreach.listOnglet.first && $smarty.foreach.listOnglet.last} single{elseif $smarty.foreach.listOnglet.first} first{elseif $smarty.foreach.listOnglet.last} last{else} render-{$keyOnglet}{/if}" {if !$smarty.foreach.listOnglet.first}style="display:none;"{/if}>
				{foreach name=bloc from=$onglet["render"] key=key item=render}
					{$render}
				{/foreach}
				<div class="clear"></div>
			</div>
		{/foreach}
	</div>
	
	<script type="text/javascript">
	$(document).ready(function(){

		var onglets = $("div.bloc-{$id} li.onglet");
		
		onglets.live("mouseover", function() { $(this).addClass("hover"); });
		onglets.live("mouseout", function() { $(this).removeClass("hover"); });
		onglets.live("click", function(){
			onglets.removeClass("selected");
			$(this).addClass("selected").parent().next().find("div.render").hide().eq($(this).index()).show();
		});
		
	});
	</script>
{/if}
