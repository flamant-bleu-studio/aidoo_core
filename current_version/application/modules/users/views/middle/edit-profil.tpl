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

<div id="user_edit">
	<h2>{t}Edit your profil{/t}</h2>
		
		<form id="{$form->getId()}" action="{$form->getAction()}" method="{$form->getMethod()}" enctype="{$form->getEnctype()}">
			
			<div class="left">
			
				{$form->username}
				
				{$form->civility}
				
				{*{if $info_profil['civility']}
					<div class="line"><span class="label">Civilité :</span> <span class="value"> {$info_profil['civility']}</span></div>
				{/if}*}
				{if $info_profil['firstname']}
					<div class="line"><span class="label">Prénom :</span> <span class="value"> {$info_profil['firstname']}</span></div>
				{/if}
				{if $info_profil['lastname']}
					<div class="line"><span class="label">Nom :</span> <span class="value"> {$info_profil['lastname']}</span></div>
				{/if}
				{if $info_profil['email']}
					<div class="line"><span class="label">Email :</span> <span class="value"> {$info_profil['email']}</span></div>
				{/if}

			</div>
			
			<div class="left" style="margin-left: 50px;">
			{foreach from=$form->getSubForm('metas')->getElements() item=element}
				{assign var=temp_name_element value=$element->getName()}
				{$form->metas->$temp_name_element}
			{/foreach}
			
			{$form->submit}
			</div>
		</form>
		
</div>
