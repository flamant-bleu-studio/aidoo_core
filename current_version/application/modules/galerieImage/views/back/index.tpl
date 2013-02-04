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
	<h1>{if $type=="diaporama"}{t}Liste des diaporamas{/t}{elseif $type=="galerie"}{t}Liste des galerie photo{/t}{/if}</h1>
	<div>{if $type=="diaporama"}Gestion des diaporamas{elseif $type=="galerie"}Gestion des galerie photos{/if}</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{if $type=="diaporama"}{t}Diaporamas{/t}{/if}{if $type=="galerie"}{t}Galerie Photo{/t}{/if} <span class="helper"></span></h2>
		<div>Créez, éditez, supprimez</div>
	</div>
	
	{if $backAcl->hasPermission($namePermission, "create")}
		<div class="pull-left btn_label">
			<div>{if $type=="diaporama"}{t}Add a new diaporama{/t}{elseif $type=="galerie"}{t}Add a new galerie photo{/t}{/if}</div>
			<div class="info_details">{if $type=="diaporama"}{t}Create your diaporama{/t}{elseif $type=="galerie"}{t}Create your galerie photo{/t}{/if}</div>
		</div>
	
		<a href='{routeShort action="create"}' class="btn btn-success">
			<div><i class="icon-plus icon-white"></i> {t}Add{/t}</div>
		</a>
	{/if}
	

	<table class="datatable table table-bordered table-striped" id="datatable">
		<thead>
			<tr>
				<th><span>{t}Title{/t}</span></th>
				<th><span>{t}Number of images{/t}</span></th>
				<th class="no_sorting" style="width:125px;"><span>{t}Actions{/t}</span></th>
			</tr>
		</thead>
		<tbody>
		{if $content|@count > 0}
			{foreach from=$content key=key item=v name=foo}
				<tr class='{cycle values="first,second"}'>
				
					<td>{$v->title}</td>
					<td>{$v->nb_image}</td>
					<td>
						<div class="actions">
							{if $backAcl->hasPermission({$namePermission|cat:"-"|cat:$v->id}, "edit")}
								<a class="edit btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id}"><i class="icon-pencil icon-white"></i></a>
							{/if}
							{if $backAcl->hasPermission({$namePermission|cat:"-"|cat:$v->id}, "delete")}
								<a class="delete btn btn-danger btn-mini" href="{routeShort action="delete" id=$v->id}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this diaporama ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
							{/if}

						</div>
					</td>
				</tr>
			{/foreach}
		{/if}
		</tbody>
	</table>

</div>

{if $backAcl->hasPermission($namePermission, "manage")}

<div class="content_titre">
		<h1>{t}OPTIONS{/t}</h1>
		<div>{t}CHOOSE OPTIONS{/t}</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}MODULE RIGHTS{/t}</h2>
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
