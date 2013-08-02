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
	<h1>Création d'un bloc</h1>
	<div>Mise à disposition d'un nouveau bloc sur votre site</div>
</div>

<form method="post" action="{$form->getAction()}" id="{$form->getId()}">
	<div class="zone">
		<div class="zone_titre">
			<h2>Informations<span class="helper"></span></h2>
			<div>Remplissez les informations nécessaires</div>
		</div>
					
			{$form->from}
			
			{$form->designation}
			{$form->title}
			
			{$form->decorator}
			{$form->templateFront}
			{$form->theme}
			{$form->classCss}
			
			{$blocAdmin}
	</div>
	<div class="zone">
			<div class="zone_titre">
				<h2>{t}Rights{/t}</h2>
				<div>{t}Manage rights{/t}</div>
			</div>

			<div class="droits_content">		
				{$form->permissions}
			</div>
	</div>
			
	{formButtons cancelLink="{routeShort action='index'}"}		

</form>
