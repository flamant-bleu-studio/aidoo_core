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
	<h1>{t}Users Manager{/t}</h1>
	<div>{t}Users Manager{/t}</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Users Manager{/t}</h2>
		<div>{t}Users Manager{/t}</div>
	</div>
	
	<a href="{routeShort action="create-user"}" class="btn btn-primary">
		<i class="icon-plus icon-white"></i> {t}Add new user{/t}
	</a>

	{if $backAcl->hasPermission("mod_users", "manage")}
		<a title="{t}Edit options{/t}" href="{routeShort action="edit-options-users"}" class="btn btn-warning fancybox">
			<i class="icon-pencil icon-white"></i> {t}Options{/t}
		</a>
	{/if}
			
	<a title="{t}Edit options{/t}" href="{routeShort action="export"}" class="btn btn-success">
		<i class="icon-download-alt icon-white"></i> {t}Export{/t}
	</a>
		
	<div class="datatable">
		<table class="table table-bordered table-striped" id="datatable">
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
						
						{if $userId != $v->id}
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

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Groups Manager{/t} <span class="helper"></span></h2>
		<div>{t}Groups Manager{/t}</div>
	</div>
	
	<div class="pull-left btn_label">
		<div>{t}Add group{/t}</div>
		<div class="info_details">{t}Create a new user group{/t}</div>
	</div>
	
	<a href="{routeShort action="create-group"}" class="btn btn-primary">
		<i class="icon-plus icon-white"></i> {t}Add{/t}
	</a>


	<table class="table table-bordered table-striped" >
		<thead>
			<tr>
				<th>{t}Name{/t}</th>
				<th style="width: 96px;">Actions</th>
			</tr>
		</thead>
		<tbody>
			{if $groups != null}
			{foreach from=$groups key=key item=v}
					<tr>
					 	 <td>{$v->level} {$v->name}</td>
					     <td>
							{if $backAcl->hasPowerOn($v->id) && $groupId != $v->id}
							<a class="btn btn-primary btn-mini" href='{routeShort action="edit-group" id=$v->id}'><i class="icon-pencil icon-white"></i></a>
							{if $v->id != 2}
							<a class="btn btn-danger btn-mini" href='{routeShort action="delete-group" id=$v->id}' onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this group ?{/t}</h1><p>{t}Children groups will not be delete{/t}</p><p>{t}All users in this group will be move in Public group{/t}</p>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>{/if}
							{/if}
						</td>
					</tr>
			{/foreach}
			{else}
				<tr class='second'>
					<td colspan="6" style="text-align: center;">Aucun membre... Click Add !</td>
				</tr>
			{/if}
		</tbody>
	</table>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}View-Access Manager{/t} <span class="helper"></span></h2>
		<div>{t}View-Access Manager{/t}</div>
	</div>
	
	
	<div class="pull-left btn_label">
		<div>{t}Add view-access{/t}</div>
		<div class="info_details">{t}Create a new view-access level{/t}</div>
	</div>
	<a href="{routeShort action="create-viewaccess"}" class="btn btn-primary">
		<i class="icon-plus icon-white"></i> {t}Add{/t}
	</a>


	<table class="table table-bordered table-striped" >
		<thead>
			<tr>
				<th>{t}Name{/t}</th>
				<th style="width: 96px;">Actions</th>
			</tr>
		</thead>
		<tbody>
		{if $viewAccess != null}
		{foreach from=$viewAccess key=key item=v}
			<tr>
			 	 <td>{$v->name}</td>
			     <td>
					<a class="btn btn-primary btn-mini" href='{routeShort action="edit-viewaccess" id=$v->id}'><i class="icon-pencil icon-white"></i></a>
					<a class="btn btn-danger btn-mini" href='{routeShort action="delete-viewaccess" id=$v->id}' onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this view-access ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
				</td>
			</tr>
		{/foreach}
		{else}
			<tr class='second'>
				<td colspan="6" style="text-align: center;">Aucun membre... Click Add !</td>
			</tr>
		{/if}
		</tbody>
	</table>
</div>
<script type="text/javascript">
{literal}
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
{/literal}
</script>
