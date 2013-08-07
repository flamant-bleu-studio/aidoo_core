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
	
	{foreach from=$items item=entry_item name=item}
		
		<li id="cat-{$entry_item->id_menu}" class="{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "move")}drag{/if}{if is_array($entry_item->children)} dnd_listClosed{/if}">
			<dl {if $entry_item->type|in_array:$type_folder} class="drop"{/if}> 
				{if $entry_item->type|in_array:$type_folder}<a href="#" class="dnd_expander"></a>{/if}
				<dt class="grip">
					{if $entry_item->type|in_array:$type_folder}
						<i class="icon-folder-open dnd_move {if !$entry_item->active}disabled{/if}"></i>
					{else}
						<img src="{$baseUrl}{$skinUrl}/js/dndList/middle.png" />	
						<i class="icon-file dnd_move {if !$entry_item->active}disabled{/if}"></i>
					{/if}
				</dt>
				
				<dd class="title">
					<span class="left">{$entry_item->label}</span>
				</dd> 
				
				<dt class="actions pull-right">
					{if $entry_item->type|in_array:$type_folder}
					
						{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "edit")}
						<a class="btn btn-mini btn-primary iframe showTooltip" href="{routeShort action="edit-folder" id=$entry_item->id_menu}" title='{t}Edit{/t} "{$entry_item->label|escape}"'>
							<i class="icon-pencil icon-white"></i>
						</a>
						{/if}
						
						{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_item->menu_id, "insert")}
						<a class="btn btn-mini btn-success iframe showTooltip" href="{routeShort action="add-item" id=$entry_item->menu_id elem=$entry_item->id_menu}" title='{t}Insert page into{/t} "{$entry_item->label|escape}"'>
							<i class="icon-plus icon-white"></i> <i class="icon-file icon-white"></i> 
						</a>
						<a class="btn btn-mini btn-success iframe showTooltip" href="{routeShort action="add-folder" id=$entry_item->menu_id  elem=$entry_item->id_menu}" title='{t}Insert sub folder into{/t} "{$entry_item->label|escape}"'>
							<i class="icon-plus icon-white"></i> <i class="icon-folder-open icon-white"></i>
						</a>
						{/if}
					{else}
						{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "edit")}
						<a class="btn btn-mini btn-primary iframe showTooltip" href="{routeShort action="edit-item" id=$entry_item->id_menu}" title='{t}Edit{/t} "{$entry_item->label|escape}"'>
							<i class="icon-pencil icon-white"></i>
						</a>
						{/if}
						
						{if $entry_item->isEditableContent()}
						<a class="btn btn-mini btn-primary showTooltip iframe" href="{routeShort action="edit-content" id=$entry_item->id_menu}" title="{t}Edit content of this page{/t}">
							<i class="icon-pencil icon-white"></i> <i class="icon-file icon-white"></i>
						</a>
						{/if}
						
					{/if}
					
					{if $entry_item->active}
						<a href="{routeShort action="disable" id=$entry_item->id_menu}" class="btn btn-mini btn-warning showTooltip" title='{t}Deactive{/t} "{$entry_item->label|escape}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{else}
						<a href="{routeShort action="enable" id=$entry_item->id_menu}" class="btn btn-mini btn-success showTooltip" title='{t}Active{/t} "{$entry_item->label|escape}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{/if}
					
					{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "delete")}
						{if $entry_item->id_menu && $entry_item->type|in_array:$type_folder}
							<a href="{routeShort action="delete-folder" id=$entry_item->id_menu}" class="btn btn-mini btn-danger showTooltip confirmDeleteMenuFolder" title='{t}Delete{/t} "{$entry_item->label|escape}"'>
								<i class="icon-trash icon-white"></i>
							</a>
						{elseif $entry_item->id_menu && $entry_item->isDeletableContent()}
							<a href="{routeShort action="delete-item" id=$entry_item->id_menu}" class="btn btn-mini btn-danger showTooltip confirmDeleteMenuItemWhitContent" title='{t}Delete{/t} "{$entry_item->label|escape}"'>
								<i class="icon-trash icon-white"></i>
							</a>
						{else}
							<a href="{routeShort action="delete-item" id=$entry_item->id_menu}" class="btn btn-mini btn-danger showTooltip confirmDeleteMenuItem" title='{t}Delete{/t} "{$entry_item->label|escape}"'>
								<i class="icon-trash icon-white"></i>
							</a>
						{/if}
					{/if}
					
				</dt>
				
			</dl>
			
			{if $entry_item->children|@count > 0}
				<ul>
					{call name=display_items items=$entry_item->children}
				</ul>
			{/if}
		</li>
		
	{/foreach}
	
{/function}

{include file="{$smarty.const.APPLICATION_PATH}/modules/menu/views/back/menu.tpl" active="menus"}

<div id="content">
	
	{if $backAcl->hasPermission("mod_menu", "create")}
		<a href="{routeShort action="add-menu"}" class="btn btn-primary pull-left iframe">
			<i class="icon-plus icon-white"></i> {t}Add new menu{/t}
		</a>
	{/if}
	
	<div class="mutiple_btn pull-right">
		<a href="#" title="{t}Collapse All{/t}" class="btn closeallmenu showTooltip" ><i class="icon-resize-small"></i></a>
		<a href="#" title="{t}Expend All{/t}" class="btn openallmenu showTooltip" ><i class="icon-resize-full"></i></a>
	</div>
	
	<div class="clearfix"></div>
	
	<hr />
	
	{if empty($menus)}
		{t}None{/t}
	{else}
		
		{foreach from=$menus item=entry_menu name=menu}
			
			<div class="menu_list">
				<div class="pull-right">
					<a class="btn btn-success iframe" href="{routeShort action="add-folder" id=$entry_menu->id_menu}">
						<i class="icon-folder-open icon-white"></i> {t}Add new folder{/t}
					</a>
					
					{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "insert")}
					<a class="btn btn-success iframe" href="{routeShort action="add-item" id=$entry_menu->id_menu}">
						<i class="icon-file icon-white"></i> {t}Add new page{/t}
					</a>
					{/if}
					
					{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "edit")}
					<a class="btn btn-primary showTooltip iframe" href="{routeShort action="edit-menu" id=$entry_menu->id_menu}" title='{t}Edit menu{/t} "{$entry_menu->label|escape}"'>
						<i class="icon-pencil icon-white"></i>
					</a>
					{/if}
					
					{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "delete")}
					<a class="btn btn-danger showTooltip confirmDeleteMenu" href="{routeShort action="delete-menu" id=$entry_menu->id_menu}" title='{t}Delete menu{/t} "{$entry_menu->label|escape}"'>
						<i class="icon-trash icon-white"></i>
					</a>
					{/if}
				</div>
				
				<div class="headMenu pull-left">
					<i class="icon-reorder"></i>
					<span class="title">{$entry_menu->label}</span>
					<span class="subtitle">{$entry_menu->subtitle}</span>
				</div>
				
				<div class="clearfix"></div>
				
				{if !empty($entry_menu->items)}
					<ul id="menu_{$entry_menu->id_menu}" class="dndList">
						{call name=display_items items=$entry_menu->items}
					</ul>
				{/if}
				
				<div class="clearfix"></div>
			</div>
			
			<hr />
			
		{/foreach}
	{/if}
</div>