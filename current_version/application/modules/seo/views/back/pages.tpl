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

{include file="{$smarty.const.APPLICATION_PATH}/modules/seo/views/back/menu.tpl" active="pages"}

<div id="content">
	
	{if $backAcl->hasPermission("mod_seo", "manage")}
		<a class="btn btn-primary iframe" href="{routeShort action="create-page"}"><i class="icon-plus icon-white"></i> {t}Add core page{/t}</a>
		<hr />
	{/if}
	
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}Rewrite{/t}</th>
				<th>{t}URL{/t}</th>
				<th>{t}Type{/t}</th>
				<th style="width:60px;" class="no_sorting">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		{if $pages != null}
		 {foreach from=$pages item=page}
		 
			<tr class="page_{$page->id_page}">
				<td class="title">{$page->title}</td>
				<td class="url_rewrite">{$page->url_rewrite}</td>
				<td>{$page->url_system}</td>
				<td>{$page->type}</td>
				<td>
					<a title="{t}Edit{/t}" class="iframe edit btn btn-primary btn-mini" href="{routeShort action="edit-page" id=$page->id_page}"><i class="icon-pencil icon-white"></i></a>
					
					{if $backAcl->hasPermission("mod_seo", "manage")}
						<a title="{t}Delete{/t}" data-id="{$page->id_page}" data-url="{$page->url_system}" class="error deletepage btn btn-danger btn-mini" href="{routeShort action="delete-page" id=$page->id_page}" ><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>
	
</div>