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

{include file="{$smarty.const.APPLICATION_PATH}/modules/blocs/views/back/menu.tpl" active="permissions"}

<div id="content">
	<form method="post" id="{$formAcl->getId()}"> 
		{$formAcl}
		
		<ul class="unstyled">
			<li><span class="bleu">Manage :</span> éditer les droits</li>
			<li><span class="bleu">View :</span> voir le module et son contenu</li>
			<li><span class="bleu">ViewBloc :</span> voir la liste des blocs</li>
			<li><span class="bleu">CreateBlocs :</span> créer de nouveaux blocs</li>
			<li><span class="bleu">EditTemplates :</span> éditer les templates (déplacer les blocs, ajouter de nouveaux blocs, supprimer les blocs, enregister)</li>
			<li><span class="bleu">DeleteTemplates :</span> supprimer les templates</li>
			<li><span class="bleu">CreateTemplates :</span> créer des templates</li>
		</ul>
		
		<button class="btn btn-large btn-success">{t}Submit{/t}</button>
	</form>
</div>