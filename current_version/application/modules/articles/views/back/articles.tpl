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

{include file="{$smarty.const.APPLICATION_PATH}/modules/articles/views/back/menu.tpl" active="articles"}

<div id="content">

{if $backAcl->hasPermission("mod_articles", "create")}
	<a title="{t}Add{/t}" href="{routeShort action="create"}" class="btn btn-success">
		<i class="icon-plus icon-white"></i>{t}Add{/t}
	</a>
{/if}

{if $backAcl->hasPermission("mod_articles", "manage")}
	<a title="{t}Edit rights{/t}" href="{routeShort action="edit-options-article"}" class="btn btn-warning iframe">
		<i class="icon-pencil icon-white"></i>{t}Options{/t}
	</a>
{/if}

{if $submittedArticles}

<hr />

	<h3>{t}Suggested articles list{/t} <span class="helper"></span></h3>
	
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable">
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
						<a title='{t}Edit{/t} "{$v->title}"' class="btn btn-primary btn-mini iframe" href="{routeShort action="edit" id=$v->id_article}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "delete")}
						<a title='{t}Delete{/t} "{$v->title}"' class="btn btn-danger btn-mini confirmDeleteArticle" href="{routeShort action="delete" id=$v->id_article}"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}

<hr />
	
	<table class="table table-bordered table-striped table-hover dataTable">
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
						<a title='{t}Edit{/t} "{$v->title}"' class="btn btn-primary btn-mini iframe" href="{routeShort action="edit" id=$v->id_article}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_articles-"|cat:$v->id_article, "delete")}
						<a title='{t}Delete{/t} "{$v->title}"' class="btn btn-danger btn-mini confirmDeleteArticle" href="{routeShort action="delete" id=$v->id_article}"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>

</div>