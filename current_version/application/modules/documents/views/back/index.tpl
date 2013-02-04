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
	<h1>{t}My pages{/t}</h1>
	<div>{t}Manage my pages{/t}</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}List of your documents{/t}</h2>
		<div>{t}All documents of your website{/t}</div>
	</div>
	
	{if $backAcl->hasPermission("mod_documents", "create")}
	<div class="pull-left btn_label">
		<div>{t}Add document{/t}</div>
		<div class="info_details">{t}Create a new document{/t}</div>
	</div>
	
	<a href="{routeShort action="create"}" class="btn btn-success">
		<div><i class="icon-plus icon-white"></i> {t}Add{/t}</div>
	</a>

	{/if}

	<table class="datatable table table-bordered table-striped" id="datatable">
		<thead>
			<tr>
				<th><span>{t}Title{/t}</span></th>
				<th style="width:13%;"><span>{t}Creation{/t}</span></th>
				<th style="width:15%;"><span>{t}Last update{/t}</span></th>
				<th style="width:8%;"><span>{t}Status{/t}</span></th>
				<th class="no_sorting" style="width:8%;"><span>{t}Actions{/t}</span></th>
			</tr>
		</thead>
		<tbody>
	{if $c != null}
		 {foreach from=$c item=v}
		 
			<tr>
				
				 <td>{$v->title}</td>
				 <td><span title="{formatDate format="YYYMMddHHmm" date=$v->date_add}"></span>{formatDate format="dd/MM/YY - HH:mm" date=$v->date_add}</td>
				 <td><span title="{formatDate format="YYYMMddHHmm" date=$v->date_upd}"></span>{formatDate format="dd/MM/YY - HH:mm" date=$v->date_upd}</td>
				 <td>
				 {if $v->status}
					{t}Published{/t}
				 {else}
					{t}Drafted{/t}
				 {/if}
				 </td>
				
				 <td>
					{if $backAcl->hasPermission("mod_documents-"|cat:$v->id_document, "edit")}
						<a title="{t}Edit{/t}" class="edit btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id_document}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_documents-"|cat:$v->id_document, "delete")}
						<a title="{t}Delete{/t}" class="delete btn btn-danger btn-mini" href="{routeShort action="delete" id=$v->id_document}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this document ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>

</div>

{if $backAcl->hasPermission("mod_documents", "manage")}
	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>{t}Choose your options{/t}</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
		</div>
			
		<form action="{$formAcl->getAction()}" method="post"> 
			<div class="droits_content">
				{$formAcl}
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
				</ul>
			</div>
			<div class="droits_submit">
				{$formAcl->submit}
			</div>
		</form> 
					
	</div>
{/if}	
	
