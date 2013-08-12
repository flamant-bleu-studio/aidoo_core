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

{include file="{$smarty.const.APPLICATION_PATH}/modules/diaporama/views/back/menu.tpl" active="diaporamas"}

<div id="content">
	
	{if $backAcl->hasPermission('mod_diaporama', "create")}
		<a href='{routeShort action="create"}' class="btn btn-success">
			<div><i class="icon-plus icon-white"></i> {t}Add{/t}</div>
		</a>
	{/if}
	
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable">
		<thead>
			<tr>
				<th><span>{t}Title{/t}</span></th>
				<th class="no_sorting"><span>{t}Actions{/t}</span></th>
			</tr>
		</thead>
		
		<tbody>
		{if $diaporamas|@count > 0}
			{foreach from=$diaporamas key=key item=v name=foo}
				<tr class='{cycle values="first,second"}'>
				
					<td>{$v->title}</td>
					<td>
						<div class="actions">
							{if $backAcl->hasPermission({'mod_diaporama'|cat:"-"|cat:$v->id}, "edit")}
								<a class="btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id}"><i class="icon-pencil icon-white"></i></a>
							{/if}
							{if $backAcl->hasPermission({'mod_diaporama'|cat:"-"|cat:$v->id}, "delete")}
								<a class="btn btn-danger btn-mini confirmDeleteDiaporama" href="{routeShort action="delete" id=$v->id}"><i class="icon-trash icon-white"></i></a>
							{/if}

						</div>
					</td>
				</tr>
			{/foreach}
		{/if}
		</tbody>
	</table>
</div>