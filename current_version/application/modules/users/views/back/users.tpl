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

{include file="{$smarty.const.APPLICATION_PATH}/modules/users/views/back/menu.tpl" active="users"}

<div id="content">

<a href="{routeShort action="create-user"}" class="btn btn-primary">
	<i class="icon-plus icon-white"></i> {t}Add new user{/t}
</a>

<a href="{routeShort action="export"}" class="btn btn-success">
	<i class="icon-download-alt icon-white"></i> {t}Export{/t}
</a>

<hr />

<div class="datatable">
	<table class="table table-bordered table-striped table-hover" id="datatable">
		<thead>
			<tr>
				<th>{t}Fullname{/t}</th>
				<th>{t}Email{/t}</th>
				<th>{t}Group{/t}</th>
				<th style="width: 96px;">Actions</th>
			</tr>
		</thead>
		<tbody>
		{if $users != null}
		 {foreach from=$users key=key item=v}
		 
			<tr>
			 	 <td>{$v->civility} {$v->firstname} {$v->lastname}</td>
			 	 <td>{$v->email} 
			 	 
				 	 {if $v->isConfirm}
				 		<span class="label label-info pull-right">{t}Certified{/t}</span>
				 	 {else}
				 		<span class="label label-warning pull-right">{t}Not certified{/t}</span>
				 	 {/if}
				 </td>
			 	 <td>{$v->group->name}</td>

			     <td>
					<a class="btn btn-primary btn-mini" href='{routeShort action="edit-user" id=$v->id}'><i class="icon-pencil icon-white"></i></a>
					
					{if $user->id != $v->id}
						{if $v->isActive}
							<a href="{routeShort action="deactive-user" id=$v->id}" class="btn btn-mini btn-warning" title='{t}Deactive{/t}'>
								<i class="icon-off icon-white"></i>
							</a>
						{else}
							<a href="{routeShort action="active-user" id=$v->id}" class="btn btn-mini btn-success" title='{t}Active{/t}'>
								<i class="icon-off icon-white"></i>
							</a>
						{/if}
				
						<a class="btn btn-danger btn-mini" href="{routeShort action="delete-user" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure you want to delete this account ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}'); return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
					
					</div>
				</td>
			</tr>
		
		 {/foreach}
		{/if}
		</tbody>
	</table>
	<div class="clearfix"></div>
</div>
</div>