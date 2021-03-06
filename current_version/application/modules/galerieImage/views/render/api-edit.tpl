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
	var incrementDiapo = '{$datas|@count}';
</script>

<div class="content_titre">
	<h1>{t}Edit a GaleriePhoto{/t}</h1>
	<div>Edition de votre galerie photo</div>
</div>

	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Définition des informations{/t} <span class="helper"></span></h2>
			<div>Modification des informations</div>
		</div>

		{$form->ordre_image}
		
		<div class="left">
			{$form->title}
			
			<div class="left">{$form->bg_color}</div>
			{$form->style}
			
			<div id="details_style_diapo">
				{$form->transition}
				{$form->controls_position}
				{$form->controls_style}
				{$form->autostart}
				<div class="clearfix"></div>
			</div>
		</div>
		
		<div class="clearfix"></div>
	</div>

<div class="zone">
	<div class="zone_titre">
		<h2>Ajouter des informations spécifiques / Nombre d'image(s) : <span id="nbImage"></span><span class="helper"></span></h2>
		<div>Rajouter des informations</div>
	</div>
	 
	<div id="image_manage">
		{if $datas|@count > 0}
			{foreach from=$datas key=key item=item}
				<div class='image'>
					<a href='#' class='image_delete'></a>
					<img class='galerie_image absolute' width='100' height='80' rel='{$key}' src='{$item.path_thumb}'/>
				</div>
			{/foreach}
		{/if}
	</div>
	
	<div id="image_add" class="image_add"></div>
	
	<div class="clear"></div>
	
	<div id="form_galerie" style="display:none;">

	{if $datas|@count > 0}
		{foreach from=$datas key=key item=item}
		
			<input name="datas[{$key}][path]" type="text" value="{$item.path}">
			<input name="datas[{$key}][path_thumb]" type="text" value="{$item.path_thumb}">
			
			<input name="datas[{$key}][width]" type="text" value="{$item.width}">
			<input name="datas[{$key}][height]" type="text" value="{$item.height}">
			<input name="datas[{$key}][thumb_width]" type="text" value="{$item.thumb_width}">
			<input name="datas[{$key}][thumb_height]" type="text" value="{$item.thumb_height}">
			
			<textarea name="datas[{$key}][description]">{$item.description}</textarea>
			
			<input name="datas[{$key}][bg_color_image]" type="text" value="{$item.bg_color_image}">
			<input name="datas[{$key}][date_start]" type="text" value="{$item.date_start}">
			<input name="datas[{$key}][date_end]" type="text" value="{$item.date_end}">
			
			<input name="datas[{$key}][external_page]" type="text" value="{$item.external_page}">
			<input name="datas[{$key}][page_link]" type="text" value="{$item.page_link}">
			
			<input name="datas[{$key}][isPermanent]" type="text" value="{$item.isPermanent}" />
			<input name="datas[{$key}][addLink]" type="text" value="{$item.addLink}" />
			<input name="datas[{$key}][window]" type="text" value="{$item.window}" />
			<input name="datas[{$key}][link_type]" type="text" value="{$item.link_type}">
			
		{/foreach}
	{/if}

	</div>
	<div class="clearfix"></div>
</div>

<div id="form_template" style="display:none;">
	<input name="path" type="text" value="">
	<input name="path_thumb" type="text" value="">
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
	<div id="edit_image">
		<div id="formImage">
			<div class="zone_titre">
				<h2>Edition d'une image</h2>
				<div>Gérer les paramètres de cette image</div>
			</div>
			
			{$formImage->image}
			{$formImage->description}
			{$formImage->bg_color_image}
			
			<button id="valid_image">Valider</button>
			<button id="cancel_image">Annuler</button>
		</div>
	</div>
</div>
