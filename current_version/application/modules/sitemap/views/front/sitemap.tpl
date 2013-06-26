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

{function name=display_items}
	<ul>
		{foreach from=$items item=item}
			{if !in_array($item->id, $itemsToExclude)}
				<li>
					{if !in_array($item->id_menu, $itemsToExclude)}
						<a href="{$item->getLink()}">{$item->label}</a>
					{/if}
					{if $item->children}
						<ul>
							{call name=display_items items=$item->children}
						</ul>
					{/if}
				</li>
			{/if}
		{/foreach}
	</ul>
{/function}


<div class="document">
	<h1>Plan du site</h1>
	<div id="sitemap" class="document_content">		
			
		<ul>
		{foreach from=$menus item=menu}
			
			<li id="menu_{$menu->id_menu}" class="dndList">
				{call name=display_items items=$menu->items}
			</li>
			
		{/foreach}
		
		</ul>
		
		<div class="clear"></div>
	</div>

</div>

