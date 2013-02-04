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
	<h1>{t}My articles{/t}</h1>
	<div>{t}Manage my articles{/t}</div>
</div>

{if $submittedArticles}
<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}Suggested articles list{/t} <span class="helper"></span></h2>
		<div>{t}All suggested articles of your website{/t}</div>
	</div>
	
		<table class="datatable table table-bordered table-striped show_tooltip">
			<thead>
				<tr>
					<th>{t}Title{/t}</th>
					<th>{t}Type{/t}</th>
					<th>{t}Categories{/t}</th>
					<th class="sortByDataSort">{t}Date start{/t}</th>
					<th class="no_sorting" style="width: 100px;">{t}Actions{/t}</th>
				</tr>
			</thead>
			<tbody>
			 {foreach from=$submittedArticles item=v}
			 	
				<tr>
					 <td>{$v->title}</td>
					 <td>{$v->type}</td>
					 <td>
					 {if $v->getCategories()}
						 {foreach from=$v->getCategories() item=cat}
							<span class="label label-info">{$cat->title}</span>
						 {/foreach}
					 {/if}
					 </td>
					 <td><span data-sort="{formatDate format="YYMMDDHHmm" date=$v->date_start}">{formatDate format="dd/MM/YY - HH:mm" date=$v->date_start}</span></td>
					 <td>
					 
						{if $v->status == '1'}
							<a href="{routeShort action="disable" id=$v->id_article}" class="btn btn-mini btn-warning" title='{t}Unpublish{/t} "{$v->title}"'>
								<i class="icon-off icon-white"></i>
							</a>
						{else}
							<a href="{routeShort action="enable" id=$v->id_article}" class="btn btn-mini btn-success" title='{t}Publish{/t} "{$v->title}"'>
								<i class="icon-off icon-white"></i>
							</a>
						{/if}
					 
						{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "edit")}
							<a title='{t}Edit{/t} "{$v->title}"' class="edit btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id_article}"><i class="icon-pencil icon-white"></i></a>
						{/if}
						
						{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "delete")}
							<a title='{t}Delete{/t} "{$v->title}"' class="delete btn btn-danger btn-mini" href="{routeShort action="delete" id=$v->id_article}" onClick="confirmDelete(this.href, '<h1>{t}MOD_ARTICLES_BACK_INDEX ARE YOU SUR YOU WANT DELETE ARTICLE ?{/t}</h1>', '{t}DELETE{/t}', '{t}CANCEL{/t}');return false;"><i class="icon-trash icon-white"></i></a>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
				
</div>
{/if}

<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}List of your articles{/t} <span class="helper"></span></h2>
		<div>{t}All articles of your website{/t}</div>
	</div>
	
	<div class="row-fluid">
		<div class="span6">
		
			{if $backAcl->hasPermission("mod_articles", "create")}
			<a title="{t}Add{/t}" href="{routeShort action="create"}" class="btn btn-success">
				<i class="icon-plus icon-white"></i>{t}Add{/t}
			</a>
			{/if}
			
			{if $backAcl->hasPermission("mod_articles", "manage")}
				<a title="{t}Edit rights{/t}" href="{routeShort action="edit-options-article"}" class="btn btn-warning fancybox">
					<i class="icon-pencil icon-white"></i>{t}Options{/t}
				</a>
			{/if}
		</div>
		
	</div>
	

	<table class="datatable table table-bordered table-striped show_tooltip">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}Type{/t}</th>
				<th>{t}Categories{/t}</th>
				<th class="sortByDataSort">{t}Date start{/t}</th>
				<th class="no_sorting" style="width: 100px;">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		{if $c != null}
			 {foreach from=$c item=v}
		 	
			<tr>
				 <td>{$v->title}</td>
				 <td>{$v->type}</td>
				 <td>
				 {if $v->getCategories()}
					 {foreach from=$v->getCategories() item=cat}
						<span class="label label-info">{$cat->title}</span>
					 {/foreach}
				 {/if}
				 </td>
				 <td><span data-sort="{formatDate format="YYMMDDHHmm" date=$v->date_start}">{formatDate format="dd/MM/YY - HH:mm" date=$v->date_start}</span></td>
				 <td>
				 
					{if $v->status == '1'}
						<a href="{routeShort action="disable" id=$v->id_article}" class="btn btn-mini btn-warning" title='{t}Unpublish{/t} "{$v->title}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{else}
						<a href="{routeShort action="enable" id=$v->id_article}" class="btn btn-mini btn-success" title='{t}Publish{/t} "{$v->title}"'>
							<i class="icon-off icon-white"></i>
						</a>
					{/if}
				 
					{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "edit")}
						<a title='{t}Edit{/t} "{$v->title}"' class="edit btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id_article}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "delete")}
						<a title='{t}Delete{/t} "{$v->title}"' class="delete btn btn-danger btn-mini" href="{routeShort action="delete" id=$v->id_article}" onClick="confirmDelete(this.href, '<h1>{t}MOD_ARTICLES_BACK_INDEX ARE YOU SUR YOU WANT DELETE ARTICLE ?{/t}</h1>', '{t}DELETE{/t}', '{t}CANCEL{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>
				
</div>

{if $backAcl->hasPermission("mod_categories", "view")}

<div class="content_titre">
	<h1>{t}My categories{/t}</h1>
	<div>{t}Manage my categories{/t}</div>
</div>

<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}List of your categories{/t} <span class="helper"></span></h2>
		<div>{t}All categories of your website{/t}</div>
	</div>
	
	<div class="row-fluid">
		<div class="span6">
		
			{if $backAcl->hasPermission("mod_categories", "create")}
			<a title="{t}Add{/t}" href="{routeShort action="create-category"}" class="btn btn-success fancybox">
				<i class="icon-plus icon-white"></i>{t}Add{/t}
			</a>
			{/if}
			{if $backAcl->hasPermission("mod_categories", "manage")}
				<a title="{t}Edit rights{/t}" href="{routeShort action="edit-options-category"}" class="btn btn-warning fancybox">
					<i class="icon-pencil icon-white"></i>{t}Options{/t}
				</a>
			{/if}
		</div>
		
	</div>


	<table class="datatable table table-bordered table-striped">
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
						<a title="{t}Edit{/t}" class="edit btn btn-warning btn-mini fancybox" href="{routeShort action="edit-category" id=$cat->id_categorie}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					{if $backAcl->hasPermission("mod_categories-"|cat:$cat->id_categorie, "delete")}
						<a title="{t}Delete{/t}" class="delete btn btn-danger btn-mini" href="{routeShort action="delete-category" id=$cat->id_categorie}" onClick="confirmDelete(this.href, '<h1>{t}MOD_ARTICLES_BACK_INDEX ARE YOU SUR YOU WANT DELETE ARTICLE ?{/t}</h1>', '{t}DELETE{/t}', '{t}CANCEL{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
	
</div>

{/if}

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

