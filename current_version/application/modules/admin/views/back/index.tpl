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
	<h1>{t}Dashboard{/t}</h1>
	<div>Gestion du site</div>
</div>

<div class="row-fluid">
	<div class="span6">
		<div class="zone">
			<div class="zone_titre">
				<h2>Administration du site</h2>
				<div>Accès aux fonctions les plus consultées</div>
			</div>		

			<div class="row-fluid">
	
				<div class="span6">
			
						
						<a href='{routeFull route="front"}' class="menu_dashboard btn btn-warning">
							<div class="item_icone" id="galerie"></div>
							<div class="item_title">Ma page d'accueil</div>
							<div class="item_subtitle">Votre page d'accueil</div>
						</a>
					
					
					{if $moduleExistActive["galerieImage"] == 1}
						{if $backAcl->hasPermission("mod_galeriePhoto", "view")}
						
							<a href='{routeFull route="galeriePhoto_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="galerie"></div>
								<div class="item_title">Galeries d'images</div>
								<div class="item_subtitle">Gérez vos galeries</div>
							</a>
						
						{/if}
					{/if}
					
					{if $moduleExistActive["documents"] == 1}
						{if $backAcl->hasPermission("mod_documents", "view")}
						<div>	
							<a href='{routeFull route="documents_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="docs"></div>
								<div class="item_title">Gestion des pages</div>
								<div class="item_subtitle">Gérez vos pages</div>
							</a>
						</div>
						{/if}
					{/if}		
				
					{if $moduleExistActive["skins"] == 1}
						{if $backAcl->hasPermission("mod_skins", "view")}
						<div>	
							<a href='{routeFull route="skins_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="skin"></div>
								<div class="item_title">{t}Skins Manager{/t}</div>
								<div class="item_subtitle">Gérez vos designs</div>
							</a>
						</div>
						{/if}
					{/if}
					
					{if $backAcl->hasPermission("mod_seo", "view")}
					<div>	
						<a href='{routeFull route="seo_back"}' class="menu_dashboard btn btn-warning">
							<div class="item_icone" id="ref"></div>
							<div class="item_title">Mon référencement</div>
							<div class="item_subtitle">Gérez le référencement</div>
						</a>
					</div>
					{/if}
			
				</div>
		
				<div class="span6">
					{if $backAcl->hasPermission("mod_menu", "view")}
					<div>	
						<a href='{routeFull route="menu_back"}' class="menu_dashboard btn btn-warning">
							<div class="item_icone" id="manage_page"></div>
							<div class="item_title">Gestion du menu</div>
							<div class="item_subtitle">Gérez votre menu</div>
						</a>
					</div>
					{/if}
									
					{if $moduleExistActive["users"] == 1}
						{if $backAcl->hasPermission("mod_users", "view")}
						<div>	
							<a href='{routeFull route="users_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="users"></div>
								<div class="item_title">{t}Users Manager{/t}</div>
								<div class="item_subtitle">Gérez les utilisateurs</div>
							</a>
						</div>
						{/if}
					{/if}
					
					{if $moduleExistActive["blocs"] == 1}
						{if $backAcl->hasPermission("mod_bloc", "view")}
						<div>	
							<a href='{routeFull route="blocs_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="bloc"></div>
								<div class="item_title">{t}Blocks Manager{/t}</div>
								<div class="item_subtitle">Gérez tous vos blocs</div>
							</a>
						</div>
						{/if}
					{/if}
					
					{if $moduleExistActive["galerieImage"] == 1}
						{if $backAcl->hasPermission("mod_diaporama", "view")}
						<div>	
							<a href='{routeFull route="diaporama_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="diapos"></div>
								<div class="item_title">Gestion des diaporamas</div>
								<div class="item_subtitle">Gérez vos diaporamas</div>
							</a>
						</div>
						{/if}
					{/if}

										
					{if $moduleExistActive["advertising"] == 1}
						{if $backAcl->hasPermission("mod_advertising", "view")}
						<div>	
							<a href='{routeFull route="advertising_back"}' class="menu_dashboard btn btn-warning">
								<div class="item_icone" id="pubs"></div>
								<div class="item_title">Vos Publicités</div>
								<div class="item_subtitle">Gérez vos campagnes</div>
							</a>
						</div>
						{/if}
					{/if}
							
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="zone">
			<div class="zone_titre">
				<h2>Statistiques Google Analytics</h2>
				<div>Visualisez vos statistiques</div>
			</div>
			
			{if !isset($account) && !isset($errorAuthAnalytics)}
				Configurez vos identifiants Google Analytics <a href="{routeFull route='seo_back'}">ici</a>
			{elseif !isset($account) && isset($errorAuthAnalytics)}
				Erreur lors de l'authentification : Vérifier votre compte Google et vos identifiants <a class="orange" href="{routeFull route='seo_back'}">ici</a><br /><br />
				Détails :<br />
				{$errorAuthAnalytics}
				
			{/if}
			
			<div class="liens_dashboard">
				<a class="orange" href="https://www.google.com/analytics/settings/" target="_blank">Votre compte Google Analytics</a>
			</div>
			
		</div>
	</div>
</div>
