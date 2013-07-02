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

{function name=generateBloc}
	{foreach from=$datas item=id}

		<div class="template_item {$blocs[$id]->getType()} index-{$typeIndex[$blocs[$id]->getType()]}" title="{$blocs[$id]->designation}" id="bloc-{counter}" realid="{$id}">
		{$blocs[$id]->designation}
 		
		{if $backAcl->hasPermission("mod_bloc-{$id}", "edit")}
		<a href="{routeShort action="edit" id="{$id}"}" class="editBloc"></a>
		{/if}
		
		{if $backAcl->hasPermission("mod_bloc", "editTemplates")}
		<a href="#" class="deleteBloc"></a>
		{/if}

		</div>
	{/foreach}
{/function}


<div class="content_titre">
	<h1>{t}Manage your templates{/t}</h1>
	<div>{t}Add, edit et delete your templates and blocs{/t}</div>
</div>

<div class="zone">

	<div id="templateManager" class="row-fluid">
	
		<div id="liste_bloc" class="span4 well">
			
			<div class="zone_titre">
				<h2>{t}Blocks available{/t}<span class="helper"></span></h2>
			</div>
			
			{if $backAcl->hasPermission("mod_bloc", "createBlocs")}
				<form id="{$formNew->getId()}" class="form-inline" method="post" action="{$formNew->getAction()}">
					<div class="row-fluid">
						<div class="span12">
							<strong>{t}Create new block{/t} : </strong>
							<div class="row-fluid">
								<div class="span9">{$formNew->type_new}</div>
								
									<button class="btn btn-success show_tooltip" title="{t}Add new block{/t}" id="submit_new"><i class="icon-plus icon-white"></i></button>
								
							</div>
						</div>
					</div>
					
				</form>
			{/if}
			
			{if $backAcl->hasPermission("mod_bloc", "viewBlocs")}
			<strong>{t}List of blocks available{/t}</strong>
			<ul class="unstyled">
				{if $blocsSortByType|@count > 0}
					{foreach from=$blocsSortByType key=key item=item name="foreachtype"}
					<li>
						<a href="#" class="section-head {$key} index-{$typeIndex[{$key}]}">{$blocsInfos[{$key}].name}</a>
						<ul class="section-content unstyled">
							{foreach from=$item item=bloc}
								<li class="line draggable">
									<div class="info">{$bloc->designation}</div>
									
									<div class="actions">

											{if $backAcl->hasPermission("mod_bloc-{$bloc->id_item}", "edit")}
											<a class="edit btn btn-warning btn-mini" href='{routeShort action="edit" id="{$bloc->id_item}"}' rel="{$bloc->id_item}"><i class="icon-pencil icon-white"></i></a>
											{/if}
											{if $backAcl->hasPermission("mod_bloc-{$bloc->id_item}", "delete")}
											<a class="delete btn btn-danger btn-mini" href='{routeShort action="delete" id="{$bloc->id_item}"}' rel="{$bloc->id_item}"><i class="icon-trash icon-white"></i></a>
											{/if}

									</div>
									
									<div class="infos" style="display: none;">
										<div class="id">{$bloc->id_item}</div>
										<div class="type">{$bloc->getType()} index-{$typeIndex[{$key}]}</div>
										<div class="title">{$bloc->title}</div>
									</div>
									
									<div class="clearfix"></div>
								</li>
							{/foreach}
						</ul>
					</li>
					{/foreach}
				{else}
					{t}None{/t}
				{/if}
			</ul>
			{/if}
		</div>

		<img id="arrow_right" class="show_tooltip" title="{t}Drag and drop the blocks available at the left to the layout model at the right{/t}" src="{$baseUrl}{$skinUrl}/images/bloc_arrow_right.png" />

		<div class="span8 well">
		
			<div class="zone_titre">
				<h2>{t}Templates available{/t}<span class="helper"></span></h2>
			</div>
			
				
			<div class="blocs_actions row-fluid">
			
				<div class="span10 row-fluid">
					<div class="span5">
						{$formGeneral->select_template}
					</div>
				
					<div class="span4">
					{if $backAcl->hasPermission("mod_bloc", "editTemplates")}
						<a id="configure_tpl" href="{routeShort action='edit-template-option' id=$templates[0]->id_template}" class="btn btn-warning show_tooltip" title="{t}Configure options for this template{/t}" >
							<i class="icon-pencil icon-white"></i>
							{t}Configure{/t}
						</a>
					{/if}
					</div>
				</div>
				
				<div style="text-align:right;">
				
					{if $backAcl->hasPermission("mod_bloc", "createTemplates")}
						<button class="btn btn-success show_tooltip" title="{t}Create a new template{/t}" id="new"><i class="icon-plus icon-white"></i></button>
					{/if}
					
					{if $backAcl->hasPermission("mod_bloc", "deleteTemplates")}
						<button class="btn btn-danger show_tooltip" title="{t}Delete this template{/t}"  id="delete"><i class="icon-trash icon-white"></i></button>
					{/if}
				</div>
				
			</div>
	
	
	
	
			<div class="btn-toolbar actions_tpl">
		
				{if !empty($mobileEnabled) || !empty($tabletEnable)}
					<div id="typeSelect" class="btn-group" data-toggle="buttons-radio">
						<button value="classic" class="btn active">{t}Classic{/t}</button>
						{if !empty($mobileEnabled)} 
							<button value="mobile" class="btn">{t}Mobile{/t}</button>
						{/if}
						{if !empty($tabletEnabled)}
							<button value="tablet" class="btn">{t}Touchpad{/t}</button>
						{/if}
					</div>
				{/if}
			
				{if $backAcl->hasPermission("mod_bloc", "editTemplates")}
				<div class="btn-group">
					<button class="btn btn-primary" id="submit"><i class="icon-ok icon-white"></i> {t}Save this layout{/t}</button>
				</div>
				{/if}
				
			</div>
	
			<div class="row-fluid">
				<div id="template" class="span8">
					
					<div id="t_header1" class="droppable horizontal">
						{if $templates[0]->getPlaceholder("classic", "header1")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "header1")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
					
					<div id="t_header2" class="droppable horizontal">
						{if $templates[0]->getPlaceholder("classic", "header2")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "header2")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
					
					<div id="t_header3" class="droppable horizontal">
						{if $templates[0]->getPlaceholder("classic", "header3")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "header3")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
					
					<div id="t_middle" class="horizontal">
		
						<div id="t_sideleft1" class="droppable vertical">
							{if $templates[0]->getPlaceholder("classic", "sideleft1")}
								{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "sideleft1")} 
							{/if}
							<div class="drop_zone"></div>
						</div>
		
						<div id="t_center" class="vertical">
							<div id="t_contenttop" class="droppable horizontal">
								{if $templates[0]->getPlaceholder("classic", "contenttop")}
									{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "contenttop")} 
								{/if}
								<div class="drop_zone"></div>
							</div>
							
							<div id="t_content" class="horizontal">
								<div id="t_contentleft" class="droppable vertical">
									{if $templates[0]->getPlaceholder("classic", "contentleft")}
										{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "contentleft")} 
									{/if}
									<div class="drop_zone"></div>
								</div>
								<div id="t_contentmore" class="droppable">
								
									{if $templates[0]->getPlaceholder("classic", "contentmore")}
										{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "contentmore")} 
									{/if}
									<div class="drop_zone"></div>
									
									<div class="info_ph">
										Aucun bloc ici pour un modèle de disposition déstiné à du contenu.
									</div>
					
								</div>
								<div id="t_contentright" class="droppable vertical nomarge">
									{if $templates[0]->getPlaceholder("classic", "contentright")}
										{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "contentright")} 
									{/if}
									<div class="drop_zone"></div>
								</div>
							</div>
							
							<div id="t_contentbottom" class="droppable horizontal nomarge">
								{if $templates[0]->getPlaceholder("classic", "contentbottom")}
									{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "contentbottom")} 
								{/if}
								<div class="drop_zone"></div>
							</div>
						</div>
						
						<div id="t_sideright1" class="droppable vertical nomarge">
							{if $templates[0]->getPlaceholder("classic", "sideright1")}
								{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "sideright1")} 
							{/if}
							<div class="drop_zone"></div>
						</div>
						
					</div>
		
					<div id="t_footer1" class="droppable horizontal">
						{if $templates[0]->getPlaceholder("classic", "footer1")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "footer1")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
							
					<div id="t_footer2" class="droppable horizontal">
						{if $templates[0]->getPlaceholder("classic", "footer2")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "footer2")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
						
					<div id="t_footer3" class="droppable horizontal nomarge">
						{if $templates[0]->getPlaceholder("classic", "footer3")}
							{call name=generateBloc datas=$templates[0]->getPlaceholder("classic", "footer3")} 
						{/if}
						<div class="drop_zone"></div>
					</div>
		
				</div>
			</div>
	
	
		</div>
	</div>
</div>	

{if $backAcl->hasPermission("mod_bloc", "manage")}
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
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">ViewBloc :</span> voir la liste des blocs</li>
					<li><span class="bleu">CreateBlocs :</span> créer de nouveaux blocs</li>
					<li><span class="bleu">EditTemplates :</span> éditer les templates (déplacer les blocs, ajouter de nouveaux blocs, supprimer les blocs, enregister)</li>
					<li><span class="bleu">DeleteTemplates :</span> supprimer les templates</li>
					<li><span class="bleu">CreateTemplates :</span> créer des templates</li>
				</ul>
				<div class="droits_submit">
					{$formAcl->submit}
				</div>
			</form> 
			
		</div>
	</div>
{/if}

<div style="display:none;">
	<div id="form_new_template">
		<form action='#' method="post" id="{$formTemplate->getId()}">
			<div class="zone_titre">
				<h2>{t}Create template{/t}</h2>
				<div>{t}Informations{/t}</div>
			</div>
			
			{$formTemplate->title_template}
			{$formTemplate->duplicate}
			{$formTemplate->select_template_duplicate}
			
			<div class="clearfix"></div>
			
			<div class="form_submit">
				<button class="btn btn-success" id="valid_template">Valider</button>
				<button class="btn btn-danger" id="cancel_template">Annuler</button>
			</div>

		</form>
	</div>
</div>

<input id="defaultTemplate" type="hidden" value="{$defaultTemplate}" />
<input id="backAclEditTemplates" type="hidden" value='{$backAcl->hasPermission("mod_bloc", "editTemplates")}' />

{appendScript type="css"}
#form_type_new .form_elem, #form_submit_new .form_elem{
margin-left: 0;
}

.aclBloc table#backAclManagement th 
{
padding: 0 10px;
}

{/appendScript}


<script type="text/javascript">
{literal}

	/** Get permissions mod_bloc **/
	var permission_viewBlocs = {/literal}{if $backAcl->hasPermission("mod_bloc", "viewBlocs")}true{else}false{/if}{literal};
	var permission_createTemplates = {/literal}{if $backAcl->hasPermission("mod_bloc", "createTemplates")}true{else}false{/if}{literal};
	var permission_editTemplates = {/literal}{if $backAcl->hasPermission("mod_bloc", "editTemplates")}true{else}false{/if}{literal};
	var permission_deleteTemplates = {/literal}{if $backAcl->hasPermission("mod_bloc", "deleteTemplates")}true{else}false{/if}{literal};
	
{/literal}

	$(document).ready(function() {

	    var templateManager = new $.templateManager($('#templateManager'), {
	    	tplDatas : '{$templates_info|replace:"'":"\'"}',
	    	typeIndex: {json_encode($typeIndex)},
	    	lastId : {counter},
	    	defaultTpl: '{$defaultTemplate}',
	    	homePageIdTpl: '{$idTemplate_homePage}',
	    	urlDeleteTpl: '{routeShort action="deletetemplate"}'
	    	
	    });

	});
	
	
</script>


<script type="text/javascript" src="{$baseUrl}{$skinUrl}/js/templateAdmin/templateManager.js"></script>
