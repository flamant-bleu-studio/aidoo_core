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

<style>
	.manage_zone_hide, .manage_zone_show {
		float:left;
		cursor:pointer;
		padding-right:7px;
	}
</style>

<div class="content_titre">
	<h1>Création d'un nouveau contenu</h1>
	<div>Entrez les informations associées à la page de votre contenu</div>
</div>

<form id="{$seoForm->getId()}" method="POST" action="{routeShort action="create-content"}" enctype="multipart/form-data">

	<div class="zone">
		<div class="zone_titre">
			<img title="{t}Hide this zone{/t}" class="manage_zone_hide" src="{$baseUrl}{$skinUrl}/images/move_up.png" />
			<img title="{t}Show this zone{/t}" class="manage_zone_show" src="{$baseUrl}{$skinUrl}/images/move_down.png" />
			
			<h2>SEO <span class="helper"></span></h2>
			<div>Informations relatives au référencement de cette future page</div>
		</div>
		
		<div class="content">
			{$seoForm->seo_title}
			{$seoForm->seo_url_rewrite}
			{$seoForm->seo_meta_keywords}
			{$seoForm->seo_meta_description}
		</div>
		
	</div>
	
	{$content}
	
	<div class="zone">
		<div class="zone_titre">
			<img title="{t}Hide this zone{/t}" class="manage_zone_hide" src="{$baseUrl}{$skinUrl}/images/move_up.png" />
			<img title="{t}Show this zone{/t}" class="manage_zone_show" src="{$baseUrl}{$skinUrl}/images/move_down.png" />
			
			<h2>Template & Diaporama <span class="helper"></span></h2>
			<div>Informations relatives au template et diaporama de cette future page</div>
		</div>
		
		<div class="content">
			{$templatePreview}
		</div>
		
	</div>
	
	{formButtons cancelLink="{routeShort action='index'}"}
	
</form>

<script type="text/javascript">

$(document).ready(function(){
	
	/**
	 * Show / Hide zone on click (not available for $content)
	**/
	
	$(".manage_zone_hide").each(function(){
		$(this).hide();
		$(this).parent().parent().find(".content").hide();
	});
	
	$(".manage_zone_hide").live("click", function(){
		$(this).parent().parent().find(".content").hide();
		$(this).hide();
		$(this).parent().find(".manage_zone_show").show();
	});
	
	$(".manage_zone_show").live("click", function(){
		$(this).parent().parent().find(".content").show();
		$(this).hide();
		$(this).parent().find(".manage_zone_hide").show();
	});
	
});

</script>
