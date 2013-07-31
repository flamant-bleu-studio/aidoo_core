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

<script type="text/javascript" src="{$baseUrl}{$skinUrl}/js/templateAdmin/galerieImage.js"></script>
<script type="text/javascript">
	var incrementDiapo = '0';
</script>

<div class="content_titre">
	<h1>{if $type=="diaporama"}{t}Create a diaporama{/t}{elseif $type=="galerie"}{t}Create a GaleriePhoto{/t}{/if}</h1>
	<div>{if $type=="diaporama"}Création d'un nouveau diaporama{elseif $type=="galerie"}Création d'une nouvelle galerie photo{/if}</div>
</div>

<form action='{$form->getAction()}' method="post" id="{$form->getId()}"> 
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Définition des informations{/t} <span class="helper"></span></h2>
			<div>Modification des informations</div>
		</div>

		{$form->ordre_image}

		{$form->title}
		
		{if $type=="diaporama"}
			{$form->size}
		{else if $type=="galerie"}
			{$form->bg_color}
			{$form->style}
			
			<div id="details_style_diapo">
				{$form->transition}
				{$form->controls_position}
				{$form->controls_style}
				{$form->autostart}
				<div class="clearfix"></div>
			</div>
		{/if}
		
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>Ajouter des informations spécifiques / Nombre d'image(s) : <span id="nbImage"></span><span class="helper"></span></h2>
			<div>Rajouter des informations</div>
		</div>
		 
		<div id="image_manage"></div>
		
		<div id="image_add" class="image_add"></div>
		
		<div class="clearfix"></div>
		
		<div id="form_galerie" style="display:none;"></div>
	</div>
	
	{if $backAcl->hasPermission($namePermission, "manage")}
		<div class="zone">
		
			<div class="zone_titre">
				<h2>{t}Rights{/t}</h2>
				<div>{t}Manage rights{/t}</div>
			</div>
		
			<div class="droits_content">
				{$formAcl}
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">Create :</span> créer de {if $type=="diaporama"}nouveaux diaporamas{elseif $type=="galerie"}nouvelles galeries{/if}</li>
				</ul>
			</div>
		</div>
	{/if}
	
	{formButtons cancelLink="{routeShort action='index'}"}
	
</form>

<div id="form_template" style="display:none;">
	<input name="path" type="text" value="">
	<input name="path_thumb" type="text" value="">
	<input name="path2" type="text" value="">
	<input name="path_thumb2" type="text" value="">
	<input name="width" type="text" value="">
	<input name="height" type="text" value="">
	<input name="thumb_width" type="text" value="">
	<input name="thumb_height" type="text" value="">
	<textarea name="description"></textarea>
	
	<input name="bg_color_image" type="text" value="">
	<input name="date_start" type="text" value="">
	<input name="date_end" type="text" value="">
	<input name="external_page" type="text" value="">
	<input name="page_link" type="text" value="">
	
	<input name="isPermanent" type="text" value="" />
	<input name="addLink" type="text" value="" />
	<input name="window" type="text" value="" />
	<input name="link_type" type="text" value="">
</div>

<div style="display:none;">
	<div id="edit_image" style="width:800px;">
		<form action='#' method="post" id="{$formImage->getId()}">
			<div class="zone_titre">
				<h2>Edition d'une image</h2>
				<div>Gérer les paramètres de cette image</div>
			</div>
			
			{$formImage->image}
			{$formImage->image2}
			{if $type=="diaporama"}
				{$formImage->addLink}
				<div id="link" style="display:none;">
					{$formImage->link_type}
					<div id="page_link_internal">{$formImage->page_link}</div>
					<div id="page_link_external">{$formImage->external_page}</div>
					{$formImage->window}
				</div>
			{/if}
			{$formImage->description}
			{$formImage->bg_color_image}
			{if $type=="diaporama"}
				{$formImage->isPermanent}
				<div class="clearfix"></div>
				<div id="form_date" style="display:none;">
					<div>{$formImage->date_start}</div>
					<div>{$formImage->date_end}</div>
					<div class="clearfix"></div>
				</div>
			{/if}
			<div class="form-actions center">
				<button class="btn btn-success" id="valid_image">{t}Submit{/t}</button>
				<button class="btn btn-danger" id="cancel_image">{t}Cancel{/t}</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$("#style").on("change", function(e){
		if($(this).val() == 0){
			$("#details_style_diapo").show();
		}
		else {
			$("#details_style_diapo").hide();
		}
	});
});
</script>
