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
	<div>{t}Manage my faq{/t}</div>
</div>

<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}List of your faq{/t}</h2>
		<div>{t}All faq of your website{/t}</div>
	</div>

	{if $backAcl->hasPermission("mod_faq-"|cat:$faq->id_faq, "edit")}
	<a title="{t}Add{/t}" href="{routeShort action="create-question" id=$faq->id_faq}" class="btn btn-success">
		<i class="icon-plus icon-white"></i>{t}Add{/t}
	</a>
	{/if}

	<table class="table table-bordered table-striped show_tooltip">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}NB{/t}</th>
				<th class="no_sorting" style="width: 100px;">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody id="sortable"><ul>
			{if $faq->nodes != null}
			{foreach from=$faq->nodes item=v}
		 
			<tr id={$v->id_faq_item}>
				 <td>{$v->question[$smarty.const.CURRENT_LANG_ID]}</td>
				 <td>{$v->answer[$smarty.const.CURRENT_LANG_ID]}</td>
				 <td>			 
					{if $backAcl->hasPermission("mod_faq-"|cat:$faq->id_faq, "edit")}
						<a title='{t}Edit{/t}' class="edit btn btn-primary btn-mini fancybox" href="{routeShort action="edit-question" id=$v->id_faq_item}"><i class="icon-pencil icon-white"></i></a>
					{/if}
					
					{if $backAcl->hasPermission("mod_faq-"|cat:$faq->id_faq, "edit")}
						<a title='{t}Delete{/t}' class="delete btn btn-danger btn-mini" href="{routeShort action="delete-question" id=$v->id_faq_item}" onClick="confirmDelete(this.href, '<h1>{t}Are you sure you want to delete this question ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
			{/foreach}
			{else}
			<tr>
				<td colspan="3">{t}No faq{/t}</td>
			</tr>
			{/if}
		</tbody>
	</table>
	
	<div class="zone_titre">
		<h2>{t}Configuration{/t}</h2>
		<div>{t}Update FAQ{/t}</div>
	</div>
	
	<form method="POST" id="{$form->getId()}">
	{$form->title}
	{$form->intro}
	{$form->outro}
	{$form->access}
	
	{formButtons}
	
	</form>
	
	{if $backAcl->hasPermission("mod_faq-"|cat:$faq->id, "manage")}
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
		'type'		: 'iframe',
		'onClosed': function() {parent.location.reload(true);}
	});
	
	
	
	$( "#sortable" ).sortable({
		placeholder: "ui-state-highlight",
			update: function() {  // callback quand l'ordre de la liste est changé
				var  list_order = {};
				
		        //create an array with the new order
		        $(this).find('tr').map(function(index, obj) {
		        	var input = $(obj);
		        	list_order[input.attr('id')] = index + 1;
		        });
		
// 		        $.ajaxCMS({url : "/administration/users/api/api_user/connexion", datas : {'list_order' : list_order}});
		        
				$.ajax({ // Début requète ajax
					type: "POST",
					url: baseUrl+'/ajax/faq/update-order',
// 					url: baseUrl+'/api.php/administration/faq/index',
// 					url: baseUrl+'/api.php/administration/users/api/api_user/connexion',

// 					dataType: "json",
					data: {
						'ajax_apiKey' : ajax_apiKey,
						'list_order' : list_order
					},
					cache: false,
					error: function(results){
						alert("Erreur lors du changement d'ordre de la FAQ, Actualisez la page et réessayez.");
					},
				});
			}
	});
	$( "#sortable" ).disableSelection();
});
{/literal}
</script>
