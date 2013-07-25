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

{include file="{$smarty.const.APPLICATION_PATH}/modules/seo/views/back/menu.tpl" active="types"}

<div id="content">
	
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable typesPages">
		<thead>
			<tr>
				<th><span>{t}Type{/t}</span></th>
				<th><span>{t}Title{/t}</span></th>
				<th><span>{t}Default template{/t}</span></th>
			</tr>
		</thead>
		<tbody>
		{if $types != null}
		 {foreach from=$types key=key item=type}
		 
			<tr>
			 	<td>{if $type.type->parent_type} &rarr; {/if}{$key}</td>
				<td>{$type.title}</td>
				<td>
					<select data-id="{$type.type->id_type}">
					{html_options options=$templates selected=$type.type->default_tpl}
					</select>
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>
	
</div>