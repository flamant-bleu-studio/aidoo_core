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

<div class="zone">
	
	<div class="zone_titre">
		<h2>{t}Create category{/t} <span class="helper"></span></h2>
		<div>{t}Creation of a new category{/t}</div>
	</div>
	
	<form id="{$form->getId()}" method="{$form->getMethod()}" action="{$form->getAction()}" enctype="multipart/form-data">
		{$form->parent}
		{$form->title}
		{$form->image}
		{$form->description}
		{$form->countByPage}
		{$form->typeView}
		{$form->fb_comments_number_show}
		
		{if $backAcl->hasPermission("mod_categories", "manage")}
			
			<div class="zone_titre">
				<h2>{t}Rights{/t}</h2>
				<div>{t}Manage rights{/t}</div>
			</div>
			<div class="droits_content">
				{$formAcl}
			</div>
			
		{/if}
		
		<div class="droits_submit">
			{$form->submit}
		</div>
		
	</form>
	
</div>
