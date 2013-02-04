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
	<h1>{t}Sitemap Configuration{/t}</h1>
	<div></div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Sitemap Configuration{/t} <span class="helper"></span></h2>
		<div>{t} Check the pages you want to hide from the sitemap{/t}</div>
	</div>
	
	{$sitemapForm}
	
</div>

{if $backAcl->hasPermission("mod_sitemap", "manage")}

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
