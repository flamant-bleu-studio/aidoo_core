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

<div class="zone">

	{if $backAcl->hasPermission("mod_seo", "manage")}
		<a class="btn btn-success fancybox right btn-large" style="margin-bottom:5px;" href="{routeShort action="create-page"}">{t}Add core page{/t}</a>
	{/if}
	
	<div class="zone_titre">
		<h2>Pages disponibles</h2>
		<div>Liste des pages disponible du site</div>
	</div>
	
	<table id="datatable" class="datatable table table-bordered table-striped">
		<thead>
			<tr>
				<th>{t}Title{/t}</th>
				<th>{t}Rewrite{/t}</th>
				<th>{t}URL{/t}</th>
				<th>{t}Type{/t}</th>
				<th style="width:60px;" class="no_sorting">{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
		{if $pages != null}
		 {foreach from=$pages item=page}
		 
			<tr class="page_{$page->id_page}">
				<td class="title">{$page->title}</td>
				<td class="url_rewrite">{$page->url_rewrite}</td>
				<td>{$page->url_system}</td>
				<td>{$page->type}</td>
				<td>
					<a title="{t}Edit{/t}" class="fancybox edit btn btn-primary btn-mini" href="{routeShort action="edit-page" id=$page->id_page}"><i class="icon-pencil icon-white"></i></a>
					
					{if $backAcl->hasPermission("mod_seo", "manage")}
						<a title="{t}Delete{/t}" data-id="{$page->id_page}" data-url="{$page->url_system}" class="error deletepage btn btn-danger btn-mini" href="{routeShort action="delete-page" id=$page->id_page}" ><i class="icon-trash icon-white"></i></a>
					{/if}
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>
	

	<div class="zone_titre">
		<h2>Types de pages</h2>
		<div>Liste des types de pages disponibles du site</div>
	</div>

	<table id="table_type" class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th><span>{t}Type{/t}</span></th>
				<th><span>{t}Title{/t}</span></th>
				<th><span>{t}Default template{/t}</span></th>
			</tr>
		</thead>
		<tbody>
		{if $types != null}
		 {foreach from=$types key=key item=type}
		 
			<tr>
			 	<td>{if $type.type->parent_type} &rarr; {/if}{$key}</td>
				<td>{$type.title}</td>
				<td>
					<select data-id="{$type.type->id_type}">
					{html_options options=$templates selected=$type.type->default_tpl}
					</select>
				</td>
			</tr>
		{/foreach}
		{/if}
		</tbody>
	</table>

</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Social Accounts{/t} <span class="helper"></span></h2>
		<div>{t}Social Accounts{/t}</div>
	</div>
	
	<div class="droits_content">
		{$socialForm}
	</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>Référencement par défaut</h2>
		<div>Si rien n'est renseigné pour une page, ces informations seront prises par défaut</div>
	</div>

	<div class="section_content table_section">
		{$formGeneralConfig}
	</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>Pages d'accueil</h2>
		<div>Gérez votre page d'accueil</div>
	</div>
	{$homeForm}
</div>

<div class="zone">	
	<div class="zone_titre">
		<h2>Page d'erreurs</h2>
		<div>Erreurs système et pages introuvables</div>
	</div>
	
	<form class="form-horizontal" method="post" action="{routeShort action="index"}">
		<input type="hidden" value="" name="404" />
		
		<div class="control-group">		
			<label class="control-label" for="template">Template</label>

			<div class="controls">
				<select name="template">
					{html_options options=$templates selected=$tpl_404}
				</select>
				<p class="help-block">Template de la page d'erreurs</p>
			</div>
		</div>
		
		<div id="form_home_submit" class="form_submit">
			<button class="btn btn-success" name="home_submit">
				<span>Valider</span>
			</button>	
		</div>
	</form>
</div>

	
{if $backAcl->hasPermission("mod_seo", "manage")}

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

{literal}
<script type="text/javascript">

function updateFromIframe(datas){

	var tr = $("#datatable").find(".page_" + datas.id_page);

	tr.find(".title").text(datas.title[defaultLangId]);
	tr.find(".url_rewrite").text(datas.url_rewrite[defaultLangId]);
}

$(document).ready(function(){ 

	$(".fancybox").fancybox({
		'scrolling'		: 'auto',
		'width'			: '75%',
		'height'		: '100%',
		'titleShow'		: false,
		'autoScale'		: true,
		'type'		: 'iframe'
	});

	$("#table_type").on("change", "select", function(){

		var elem = $(this),
			id = elem.attr("data-id"),
			tpl_id = elem.val();

		var parent = elem.parent();
		var html = parent.html();
		
		elem.parent().html($("<img src='"+baseUrl+"/images/loader.gif' />"));
		
		$.ajax({
			type: "POST",
			url: baseUrl+'/ajax/seo/updatetemplate',
			dataType: "json",
			data: {
				id : id,
				tpl_id : tpl_id
			},
			cache: false,
			error: function(results){
				alert("Une erreur est survenue ...\nActualisez la page et réessayez.");
			},
			success: function(results){
				if(results["error"] == true)
					alert("Une erreur est survenue :\n" + results["message"]);
				else {
					parent.html(html).find("select").val(tpl_id);
				}
			}
		});

		return false;
	});

	$('.deletepage').on('click', function(){
		if (confirm('{/literal}{t}Did you really wante to delete this page ?{/t} ("'+ $(this).data('url') +'"){literal}')) {
			$.ajaxCMS({
				url : "/seo/page/" + $(this).data('id'),
				type : "delete",
				success : function(e){
					if (!e.error) {
						$('.deletepage[data-id="' + e.id + '"]').parents('tr').hide('slow');
					}
				}
			});
		} 
		
		return false;
	});
	
});
</script>
{/literal}
