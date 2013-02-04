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

<div class="content_titre">
	<h1>{t}Create a new article{/t}</h1>
	<div>{t}Creation of a new article{/t}</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Select a type{/t} <span class="helper"></span></h2>
		<div>{t}Select a type for your new article{/t}</div>
	</div>
		
	{if $types}
		<div class="choixDoc">		
		{foreach from=$types item=item}					
			<button id="{$item["type"]}" class="typeChoice" data-choice="{$item["type"]}" data-desc="{$item["description"]}">{$item["type"]}</button>
		{/foreach}
		
			<p id="doc_types_desc" class="help-block">
				{t}Select a type for your new article{/t}
			</p>
		</div>	

	{else}
		<p class="help-block center">
			{t}No type is available{/t}
		</p>
	{/if}
	
	<script type="text/javascript">

		var desc = $("#doc_types_desc");
		desc.data("text", desc.text());
	
		$(".typeChoice").on({
			click: function(e){
				e.preventDefault();
				window.location.href = "{routeShort action="createdocument"}/" + $(this).attr('data-choice');
			},
			mouseover: function(){
				desc.text($(this).attr("data-desc"));
			},
			mouseout: function(){
				desc.text(desc.data("text"));
			}
		});
	</script>
</div>
