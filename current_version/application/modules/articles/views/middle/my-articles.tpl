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

{appendFile type="js" src="{$smarty.const.COMMON_LIB_PATH}/lib/datatables/1.9.0/media/js/jquery.dataTables.min.js"}
{appendFile type="js" src="{$smarty.const.COMMON_LIB_PATH}/lib/datatables/1.9.0/media/js/dataTables.plugins.js"}
		
<script type="text/javascript"> var datatable_lang_file =  "{$smarty.const.COMMON_LIB_PATH}/lib/datatables/1.9.0/media/lang/fr.lang"; </script>
		
<div class="datatable">
	<table id="datatable" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}Categories{/t}</th>
				<th>{t}Last update{/t}</th>
				<th>{t}Status{/t}</th>
				<th class="no_sorting">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		
	{if $articles != null}
		 {foreach from=$articles item=article}
		 
		<tr>
		 	 <td>{$article->title}</td>
		 	 <td>
		 	 {if $article->getCategories()}
			 	 {foreach from=$article->getCategories() item=cat name=listCat}
			 	 	{$cat->title}
			 	 	{if !$smarty.foreach.listCat.last}, {/if}
			 	 {/foreach}
		 	 {/if}
		 	 </td>
		 	 <td>{formatDate format="dd/MM/YY - HH:mm" date=$article->date_upd}</td>
			 <td>
				{if !$article->status}{t}Pending validation{/t}{else}{t}Published{/t}{/if}
			 </td>					
		     <td>
		    	 <a href="{routeShort action="edit-article" id=$article->id_article}">{t}Edit{/t}</a>
		    	 
				{if $article->status}
					<a href="{routeFull route="articles" action="view" id=$article->id_article}">{t}View{/t}</a>
				{/if}
			</td>
		</tr>
		{/foreach}
		{/if}
		
		</tbody>
	</table>
</div>
	
{literal}
<script> 
	$(document).ready(function(){

		if(jQuery.isFunction( jQuery.dataTable )){
			$.extend( $.fn.dataTableExt.oStdClasses, {
				"sSortAsc": "header headerSortDown",
				"sSortDesc": "header headerSortUp",
				"sSortable": "header"
			});
		}
		if($('#datatable').length){
			$('#datatable').dataTable({
				"oLanguage": {
					"sUrl": datatable_lang_file
				},
				"sPaginationType": "full_numbers",
				"aoColumnDefs": [
					{"bSortable": false, "aTargets": ["no_sorting"]}
				]
			});
		}
	}); 
</script>		
{/literal}
