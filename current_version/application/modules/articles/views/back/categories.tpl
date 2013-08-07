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

{include file="{$smarty.const.APPLICATION_PATH}/modules/articles/views/back/menu.tpl" active="categories"}

<div id="content">

	{if $backAcl->hasPermission("mod_categories", "create")}
	<a title="{t}Add{/t}" href="{routeShort action="create-category"}" class="btn btn-success iframe">
		<i class="icon-plus icon-white"></i>{t}Add{/t}
	</a>
	{/if}
	
	{if $backAcl->hasPermission("mod_categories", "manage")}
		<a title="{t}Edit rights{/t}" href="{routeShort action="edit-options-category"}" class="btn btn-warning iframe">
			<i class="icon-pencil icon-white"></i>{t}Options{/t}
		</a>
	{/if}

<hr />

{if $backAcl->hasPermission("mod_categories", "view")}
	
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th class="sortByDataSort">{t}Date add{/t}</th>
				<th class="sortByDataSort">{t}Date update{/t}</th>
				<th class="no_sorting" style="width: 65px;">{t}Actions{/t}</th>
			</tr>
		</thead>
		
		{foreach from=$categories item=cat}
			<tr>
				<td>{$cat->title}</td>
				<td>{if $cat->date_add}<span data-sort="{formatDate format="YYMMDDHHmm" date=$cat->date_add}">{formatDate format="dd/MM/YY - HH:mm" date=$cat->date_add}</span>{/if}</td>
				<td>{if $cat->date_upd}<span data-sort="{formatDate format="YYMMDDHHmm" date=$cat->date_upd}">{formatDate format="dd/MM/YY - HH:mm" date=$cat->date_upd}</span>{/if}</td>
				<td>
					{if $backAcl->hasPermission("mod_categories-"|cat:$cat->id_categorie, "edit")}
						<a title="{t}Edit{/t}" class="btn btn-warning btn-mini iframe" href="{routeShort action="edit-category" id=$cat->id_categorie}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					{if $backAcl->hasPermission("mod_categories-"|cat:$cat->id_categorie, "delete")}
						<a title="{t}Delete{/t}" class="btn btn-danger btn-mini confirmDeleteCategory" href="{routeShort action="delete-category" id=$cat->id_categorie}"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
	
</div>

{/if}