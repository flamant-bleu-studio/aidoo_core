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

{include file="{$smarty.const.APPLICATION_PATH}/modules/documents/views/back/menu.tpl" active="new-pages"}

<div id="content">
	<h3 style="text-align: center;">{t}Select a type for your new document{/t}</h3>
	
	{if $types}
		<div class="choixDoc">		
			{foreach from=$types item=item}
				<div>
					<button id="documents_{$item["type"]}" class="typeChoice" data-choice="{$item["type"]}"></button>
					<p id="doc_types_desc" class="help-block">
						{$item["description"]}
					</p>
				</div>
			{/foreach}
		</div>
	{else}
		<p>{t}No type is available{/t}</p>
	{/if}
</div>

<script>
$(document).ready(function(){
	$(".typeChoice").on('click', function(e){
		e.preventDefault();
		window.location.href = "{routeShort action="createdocument"}/" + $(this).attr('data-choice');
	});
});
</script>