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
	<h1>{t}Liste des campagnes de pub{/t}</h1>
	<div>Gestion des campagnes</div>
</div>	

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Advertisements{/t}</h2>
	</div>
	
	{if $backAcl->hasPermission("mod_advertising", "create")}
		<a href='{routeShort action="create"}' class="btn btn-success">
			<i class="icon-plus icon-white"></i> {t}Add{/t}
		</a>
	{/if}

		<table class="datatable table table-bordered table-striped">
			<thead>
				<tr>
					<th>{t}Title{/t}</th>
					<th>{t}Activation Date{/t}</th>
					<th>{t}Deactivation Date{/t}</th>
					<th style="width:125px;">{t}Actions{/t}</th>
				</tr>
			</thead>
			<tbody>
				{if $activeCampaigns != null}
			 	{foreach from=$activeCampaigns key=key item=v name=foo}
			 
				<tr>
					<td>{$v->title}</td>
					
					{if !$v->limited}
						<td>{t}Permanent{/t}</td>
						<td>{t}Permanent{/t}</td>
					{else}
						<td>{formatDate date=$v->date_start}</td>
						<td>{formatDate date=$v->date_end}</td>
					{/if}
	
					<td>
						{if $backAcl->hasPermission("mod_advertising-"|cat:$v->id, "edit")}
							{if $v->enable}
								<a class="btn btn-mini btn-warning" href="{routeShort action="disable" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to disable this campaign ?{/t}</h1>', '{t}Disable{/t}', '{t}Cancel{/t}');return false;"><i class="icon-off icon-white"></i></a>
							{else}
								<a class="btn btn-mini btn-success" href="{routeShort action="enable" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to enable this campaign ?{/t}</h1>', '{t}Enable{/t}', '{t}Cancel{/t}');return false;"><i class="icon-off icon-white"></i></a>
							{/if}						
							<a class="btn btn-mini btn-primary" title='{t}Edit{/t} "{$v->title}"' href="{routeShort action="edit" id=$v->id}"><i class="icon-pencil icon-white"></i></a>
						{/if}
						{if $backAcl->hasPermission("mod_advertising-"|cat:$v->id, "delete")}
							<a class="btn btn-mini btn-danger" title='{t}Delete{/t} "{$v->title}"' href="{routeShort action="delete" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this advert ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
						{/if}
					</td>
				</tr>
				
			 	{/foreach}
				{/if}
			</tbody>
		</table>

		<div class="zone_titre">
			<h2>{t}Archived adverts{/t} <span class="helper"></span></h2>
		</div>
	
		<table class="table table-bordered table-striped">
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}Activation Date{/t}</th>
				<th>{t}Deactivation Date{/t}</th>
				<th style="width:125px;">{t}Actions{/t}</th>
			</tr>

			{if $archivedCampaigns != null}
		 	{foreach from=$archivedCampaigns item=v name=foo}
		 
			<tr class='{cycle values="first,second"}'>
				
				<td>{$v->title}</td>
				{if $v->limited}
					<td>{t}Permanent{/t}</td>
					<td>{t}Permanent{/t}</td>
				{else}
					<td>{formatDate date=$v->date_start}</td>
					<td>{formatDate date=$v->date_end}</td>
				{/if}
				<td>
						{if $backAcl->hasPermission("mod_advertising-"|cat:$v->id, "edit")}
							{if $v->enable}
								<a class="btn btn-mini btn-warning" href="{routeShort action="disable" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to disable this campaign ?{/t}</h1>', '{t}Disable{/t}', '{t}Cancel{/t}');return false;"><i class="icon-off icon-white"></i></a>
							{else}
								<a class="btn btn-mini btn-success" href="{routeShort action="enable" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to enable this campaign ?{/t}</h1>', '{t}Enable{/t}', '{t}Cancel{/t}');return false;"><i class="icon-off icon-white"></i></a>
							{/if}						
							<a class="btn btn-mini btn-primary" title='{t}Edit{/t} "{$v->title}"' href="{routeShort action="edit" id=$v->id}"><i class="icon-pencil icon-white"></i></a>
						{/if}
						{if $backAcl->hasPermission("mod_advertising-"|cat:$v->id, "delete")}
							<a class="btn btn-mini btn-danger" title='{t}Delete{/t} "{$v->title}"' href="{routeShort action="delete" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this advert ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
						{/if}
					</td>
			</tr>
		
		 	{/foreach}
			{else}
			<tr class='second'>
				<td colspan="4" style="text-align: center;">{t}No archived advert to display ...{/t}</td>
			</tr>
			{/if}
		</table>
	</div>
	
{if $backAcl->hasPermission("mod_advertising", "manage")}

<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>Choisissez vos options</div>
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
