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

<script type="text/javascript">
	var datasPubEdit = false;
</script>

<div class="content_titre">
	<h1>{t}Create an campaign{/t}</h1>
	<div>Création d'une nouvelle campagne de pub</div>
</div>


{literal}<script type="text/javascript">var datasPubEdit = false;</script>{/literal}

<form action='{$formCampaign->getAction()}' method="post" id="{$formCampaign->getId()}" class="form_reclame"> 
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Définition des informations{/t} <span class="helper"></span></h2>
			<div>Modification des informations</div>
		</div>
		
		{$formCampaign->title}
		
		{$formCampaign->limited}
		<div class="clearfix"></div>
		<div id="limited_date" class="row-fluid">
			<div class="span6">{$formCampaign->date_start}</div>
			<div class="span6">{$formCampaign->date_end}</div>
		</div>
		
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Add advert{/t}</h2>
			<div></div>
		</div>
		
		<div id="pub_manage"></div>
		
		<div id="pub_add" class="pub_add"></div>
		
		
		<div id="form_galerie">
			{$formCampaign->datas}
			
			<div class="center">
				<a id="pub_del" class="btn btn-danger">Tout Supprimer</a>
			</div>
			
		</div>
	</div>
	
	{if $backAcl->hasPermission("mod_advertising", "manage")}
		<div class="zone">
			<div class="droits_content">
				{$formAcl}
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">Create :</span> créer de nouvelles campagnes</li>
				</ul>
			</div>
		</div>
	{/if}
	
	{formButtons cancelLink="{routeShort action='index'}"}

</form>

<div style="display:none;">
	<div id="edit_pub">
		<form action='#' method="post" id="{$formAdvert->getId()}">
			<div class="zone_titre">
				<h2>Edition d'une pub</h2>
				<div>Gérer les paramètres de cette pub</div>
			</div>
			
			{$formAdvert->image}
			{$formAdvert->link_type}
				<div id="page_link_internal">{$formAdvert->page_link}</div>
				<div id="page_link_external">{$formAdvert->external_page}</div>
			{$formAdvert->window}
			
			{$formAdvert->weight}
			
			{$formAdvert->addtext}
			<div id="addtext_text">{$formAdvert->text}</div>
			<div class="clearfix"></div>
			<div class="form-actions center">
				<button class="btn btn-success" id="valid_pub">Valider</button>
				<button class="btn btn btn-danger" id="cancel_pub">Annuler</button>
			</div>
		</form>
	</div>
</div>
