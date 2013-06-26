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
		
		<li id="cat-{$entry_item->id_menu}" class="{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "move")}drag{/if}{if is_array($entry_item->children)} dnd_listOpen{/if}">
			<dl {if $entry_item->type|in_array:$type_folder} class="drop"{/if}> 
				{if $entry_item->type|in_array:$type_folder}<a href="#" class="dnd_expander"></a>{/if}
				<dt class="grip">
					
					{if $entry_item->type|in_array:$type_folder}
						
						{if $entry_item->active}
							<img src="{$baseUrl}{$skinUrl}/images/menu_folder_on.png" class="dnd_move" />
						{else}
							<img src="{$baseUrl}{$skinUrl}/images/menu_folder_off.png" class="dnd_move" />
						{/if}
						
					{else}
						
						<img src="{$baseUrl}{$skinUrl}/js/dndList/middle.png" />	
						
						{if $entry_item->active}
							<img src="{$baseUrl}{$skinUrl}/images/menu_file_on.png" class="dnd_move" />
						{else}
							<img src="{$baseUrl}{$skinUrl}/images/menu_file_off.png" class="dnd_move" />
						{/if}
						
					{/if}
				</dt>
					
				<dd class="title">
					<span class="left">{$entry_item->label}</span>
				</dd> 
				
				<dt class="actions show_tooltip">
					{if $entry_item->type|in_array:$type_folder}
					
						{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "edit")}
						<a class="btn btn-mini btn-primary fancybox" href="{routeShort action="edit-folder" id=$entry_item->id_menu}" title='{t}Edit{/t} "{$entry_item->label}"'>
							<i class="icon-pencil icon-white"></i>
						</a>
						{/if}
						
						{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_item->menu_id, "insert")}
						<a class="btn btn-mini btn-success fancybox" href="{routeShort action="add-item" id=$entry_item->menu_id elem=$entry_item->id_menu}" title='{t}Insert page into{/t} "{$entry_item->label}"'>
							<i class="icon-plus icon-white"></i> <i class="icon-file icon-white"></i> 
						</a>
						<a class="btn btn-mini btn-success fancybox" href="{routeShort action="add-folder" id=$entry_item->menu_id  elem=$entry_item->id_menu}" title='{t}Insert sub folder into{/t} "{$entry_item->label}"'>
							<i class="icon-plus icon-white"></i> <i class="icon-folder-open icon-white"></i>
						</a>
						{/if}
					{else}
						{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "edit")}
						<a class="btn btn-mini btn-primary fancybox" href="{routeShort action="edit-item" id=$entry_item->id_menu}" title='{t}Edit{/t} "{$entry_item->label}"'>
							<i class="icon-pencil icon-white"></i>
						</a>
						{/if}
						
						{if $entry_item->isEditableContent()}
						<a class="btn btn-mini btn-primary" href="{routeShort action="edit-content" id=$entry_item->id_menu}" title="{t}Edit content of this page{/t}">
							<i class="icon-pencil icon-white"></i> <i class="icon-file icon-white"></i>
						</a>
						{/if}
						
					{/if}
					
					{if $entry_item->active}
						<a href="{routeShort action="disable" id=$entry_item->id_menu}" class="btn btn-mini btn-warning" title='{t}Deactive{/t} "{$entry_item->label}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{else}
						<a href="{routeShort action="enable" id=$entry_item->id_menu}" class="btn btn-mini btn-success" title='{t}Active{/t} "{$entry_item->label}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{/if}
					
					{if $backAcl->hasPermission("mod_menu-item-"|cat:$entry_item->id_menu, "delete")}
						{if $entry_item->id_menu && $entry_item->type|in_array:$type_folder}
							<a href="{routeShort action="delete-folder" id=$entry_item->id_menu}" class="btn btn-mini btn-danger" title='{t}Delete{/t} "{$entry_item->label}"' 
								onClick="confirmDeleteMenuFolder( this.href, this.href+'/deletechildren', this.href+'/deletechildrenandcontent',
									'<h1>{t escape='quote'}Are you sure you want to delete this folder ?{/t}</h1>', 
									'{t escape='quote'}Delete folder only{/t}',
									'{t escape='quote'}Delete folder and his items{/t}',
									'{t escape='quote'}Delete folder, his items and their content{/t}',
									'{t escape='quote'}Delete{/t}',
									'{t escape='quote'}Cancel{/t}');
								return false;">
								<i class="icon-trash icon-white"></i>
							</a>
						{elseif $entry_item->id_menu && $entry_item->isDeletableContent()}
							<a href="{routeShort action="delete-item" id=$entry_item->id_menu}" class="btn btn-mini btn-danger" title='{t}Delete{/t} "{$entry_item->label}"' 
								onClick="confirmDeleteMenuItem( this.href, this.href+'/deletecontent',
									'<h1>{t escape='quote'}Are you sure you want to delete this item ?{/t}</h1>', 
									'{t escape='quote'}Delete item only{/t}',
									'{t escape='quote'}Delete item and his content{/t}',
									'{t escape='quote'}Delete{/t}',
									'{t escape='quote'}Cancel{/t}');
								return false;">
								<i class="icon-trash icon-white"></i>
							</a>
						{else}
							<a href="{routeShort action="delete-item" id=$entry_item->id_menu}" class="btn btn-mini btn-danger" title='{t}Delete{/t} "{$entry_item->label}"' onClick="confirmDelete(this.href, '<h1>{t}Are you sure you want to delete this item ?{/t}', '{t}Delete{/t}', '{t}Cancel{/t}');return false;">
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


<div class="content_titre">
	<h1>Gestion du menu</h1>
	<div>Gérer votre menu</div>
</div>

<div class="zone">

	{if $backAcl->hasPermission("mod_menu", "create")}
		<div class="zone_titre">
			<h2>{t}Add new menu{/t}</h2>
			<div>{t}Create a new menu{/t}</div>
		</div>
	{/if}
	
	
	{if $backAcl->hasPermission("mod_menu", "create")}
		<a href='{routeShort action="add-menu"}' class="btn btn-success"><i class="icon-plus icon-white"></i> {t}Create new menu{/t}</a>
	{/if}
	<div class="mutiple_btn pull-right">
		<a href="#" title="{t}Collapse All{/t}" class="btn closeallmenu show_tooltip" ><i class="icon-resize-small"></i></a>
		<a href="#" title="{t}Expend All{/t}" class="btn openallmenu show_tooltip" ><i class="icon-resize-full"></i></a>
	</div>
	<div class="clearfix"></div>
</div>

{if $menus}
<div class="zone">		

	<div class="zone_titre">
		<h2>{t}Pages of your website{/t}</h2>
		<div>Glissez-déposez pour réordonner les élements entre-eux</div>
	</div>

	{foreach from=$menus item=entry_menu name=menu}
		
		<div class="menu_list">
			<div class="mutiple_btn pull-right">
				<a class="btn btn-success fancybox" href="{routeShort action="add-folder" id=$entry_menu->id_menu}">
					<i class="icon-folder-open icon-white"></i> {t}Add new folder{/t}
				</a>
				
				{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "insert")}
				<a class="btn btn-success fancybox" href="{routeShort action="add-item" id=$entry_menu->id_menu}">
					<i class="icon-file icon-white"></i> {t}Add new page{/t}
				</a>
				{/if}
				
				{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "edit")}
				<a class="btn btn-primary show_tooltip" href="{routeShort action="edit-menu" id=$entry_menu->id_menu}" title='{t}Edit menu{/t} "{$entry_menu->label}"'>
					<i class="icon-pencil icon-white"></i>
				</a>
				{/if}
				
				{if $backAcl->hasPermission("mod_menu-menu-"|cat:$entry_menu->id_menu, "delete")}
				<a class="btn btn-danger show_tooltip" href="{routeShort action="delete-menu" id=$entry_menu->id_menu}" title='{t}Delete menu{/t} "{$entry_menu->label}"' onClick="confirmDelete(this.href, '<h1>{t}Delete this menu ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;">
					<i class="icon-trash icon-white"></i>
				</a>
				{/if}
			</div>
			<img style="margin-left: 10px;" src="{$baseUrl}{$skinUrl}/js/dndList/icone_menu.jpg" class="left" />
			
			<div class="left btn_label" style="margin-top: 7px; text-align: left;">
				<div class="ligne1">{$entry_menu->label}</div>
				<div class="gris">{$entry_menu->subtitle}</div>
			</div>
			
			<ul id="menu_{$entry_menu->id_menu}" class="dndList">
				{call name=display_items items=$entry_menu->items}
			</ul>
			
			<script type="text/javascript">
				$(document).ready(function() {	
					name = "#menu_{$entry_menu->id_menu}";
					{literal}
					
					$(name).dndList({
						onDropInterline : function(opt){
							move_to_next_sibling_menu_node(opt.src, opt.dst);
						},
						onDropElement : function(opt){
							update_parent_menu_node(opt.src, opt.dst);
						}
					});
					
					{/literal}
				});
			</script>
			<div class="clearfix"></div>
		</div>
		
	{/foreach}
	<div class="clearfix"></div>
	
</div>

{/if}

{if $backAcl->hasPermission("mod_menu", "manage")}
	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>{t}Set your options{/t}</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
		</div>
		<div class="droits_content">
			
			<form action="{$formAcl->getAction()}" method="post"> 
				{$formAcl}
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
				</ul>
				<div class="droits_submit">
					{$formAcl->submit}
				</div>
			
			</form> 
			
		</div>
	</div>
{/if}			

<script type="text/javascript">

	$(document).ready(function() {
				
		$(".fancybox").fancybox({
			'scrolling'		: 'auto',
			'width'			: '75%',
			'height'		: '100%',
			'titleShow'		: false,
			'autoScale'		: true,
			'type'		: 'iframe'
		});		
	});

   
	{literal}
		function update_parent_menu_node(src, dst){
			var datas = {
				"src" : src,
				"dst" : dst
			};
			$.post(
				{/literal}'{$baseUrl}/ajax/menu/updateParent',{literal}
				datas, 
				function(results) {
					if (results['error'])
					{
						if (results['message'])
							alert("Une erreur est survenue :\n" + results["message"]);
						else
							alert("Une erreur est survenue ...\nActualisez la page et réessayez.");
					}
				},
				"json"
			);
		}
		
		function move_to_next_sibling_menu_node(src, dst){
			var datas = {
				"src" : src,
				"dst" : dst
			};
			$.post(
					{/literal}'{$baseUrl}/ajax/menu/moveprevioussibling',{literal}
				datas,
				function(results) {
					if (results['error'])
					{
						if (results['message'])
							alert("Une erreur est survenue :\n" + results["message"]);
						else
							alert("Une erreur est survenue ...\nActualisez la page et réessayez.");
					}
				},
				"json"
			);
		}
	{/literal}
	</script>
