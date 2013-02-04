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
	<h1>{t}Jobs{/t}</h1>
	<div>Gestion des annonces de recrutement</div>
</div>

{if $backAcl->hasPermission("mod_jobs", "edit")}
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Configuration du destinataire pour les annonces{/t}</h2>
			<div>{t}Configuration du destinataire pour les annonces{/t}</div>
		</div>
		<div class="section_content">
			<form action='{$formContact->getAction()}' method="post" id="{$formContact->getId()}"> 
				<div class="left">{$formContact->mod_jobs_contact}</div>
				<div class="left">{$formContact->save}</div>
				<div class="clearfix"></div>
			</form>
		</div>
	</div>
{/if}

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Jobs Management{/t} <span class="helper"></span></h2>
		<div>{t}Create, edit, delete{/t}</div>
	</div>

	{if $backAcl->hasPermission("mod_jobs", "create")}
		<a href='{routeShort action="create"}' class="btn btn-success">
			<i class="icon-plus icon-white"></i>{t}Add{/t}
		</a>
	{/if}

	<table class="datatable table table-bordered table-striped">
		<thead>
			<tr>
				<th>{t}Job{/t}</th>
				<th>{t}Type{/t}</th>
				<th>{t}Contact{/t}</th>
				<th style="width:165px;">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
			{if $jobs != null}
		 	{foreach from=$jobs item=v name=foo}
		 
			<tr class='{cycle values="second,first"}'>
				
				<td>{$v->job_title}</td>
				<td>{$v->contract_type}</td>
				<td>{$v->contact}</td>
				<td>

					{if $backAcl->hasPermission("mod_jobs-"|cat:$v->id, "edit")}
						<a title="{t}Edit{/t}" class="edit btn btn-warning btn-mini" href="{routeShort action="edit" id=$v->id}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					{if $backAcl->hasPermission("mod_jobs-"|cat:$v->id, "delete")}
						<a title="{t}Delete{/t}" class="delete btn btn-danger btn-mini" href='{routeShort action="delete" id=$v->id}' onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this job ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}

				</td>
			</tr>
		
		 	{/foreach}
			{/if}
		</tbody>
	</table>
</div>

{if $backAcl->hasPermission("mod_jobs", "manage")}

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
			<ul class="legende">
				<li><span class="bleu">Manage :</span> éditer les droits</li>
				<li><span class="bleu">View :</span> voir le module et son contenu</li>
				<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
			</ul>
			{$formAcl->submit}
			</form> 
			
		</div>
	</div>

{/if}	
