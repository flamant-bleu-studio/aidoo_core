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
	<h1>{t}FAQ{/t}</h1>
	<div>{t}Question / Answer{/t}</div>
</div>

<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}List of your FAQ{/t}</h2>
		<div>{t}All FAQ of your website{/t}</div>
	</div>

	{if $backAcl->hasPermission("mod_faq", "view")}
	<a title="{t}Add{/t}" href="{routeShort action="create"}" class="btn btn-success">
		<i class="icon-plus icon-white"></i>{t}Add FAQ{/t}
	</a>
	{/if}

	<table class="table table-bordered table-striped show_tooltip">
	<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th class="no_sorting" style="width: 100px;">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody id="sortable"><ul>
			{if $c != null}
			{foreach from=$c item=v}
		 
			<tr id={$v->id_faq}>
				 <td>{$v->title}</td>
				 <td>			 
					{if $backAcl->hasPermission("mod_faq-"|cat:$v->id_faq, "view")}
						<a title='{t}Edit{/t}' class="edit btn btn-primary btn-mini" href="{routeShort action="edit" id=$v->id_faq}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_faq-"|cat:$v->id_faq, "view")}
						<a title='{t}Delete{/t}' class="delete btn btn-danger btn-mini" href="{routeShort action="delete" id=$v->id_faq}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure you want to delete this question ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
			{/foreach}
			{else}
			<tr>
				<td colspan="2">{t}No faq{/t}</td>
			</tr>
			{/if}
		</tbody>
	</table>
	
</div>



{if $backAcl->hasPermission("mod_faq", "manage")}
	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>{t}Choose your options{/t}</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
		</div>
		<div class="droits_content">
			
			<form action="{$formAcl->getAction()}" method="post"> 
				<div class="aclBloc">
					{$formAcl}
				</div>
				<div class="droits_submit">
					{$formAcl->submit}
				</div>
			</form> 
			
		</div>
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
