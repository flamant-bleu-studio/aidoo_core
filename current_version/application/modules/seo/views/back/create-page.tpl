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
		<h2>{t}Edit page{/t}</h2>
		<div>{t}Page informations{/t}</div>
	</div>
	
	<div class="row-fluid">
		<div class="{if $has_rewrite_var}span8{else}span12{/if} well">
		
		<form action="{$form->getAction()}" id="{$form->getId()}" method="POST">
		
			{$form->title}
			{$form->url_rewrite}
			{$form->url_system}
			{$form->enable}
			{$form->visible}
			{$form->wildcard}
			{$form->type}
			
			{$form->meta_keywords}
			{$form->meta_description}
			
			{$form->template}
			{$form->diaporama}
	
			<div class="form_submit">
				{$form->submit}
			</div>
		</form>
		</div>
	</div>
</div>

{appendScript type="css"}

#{$form->getId()} .chzn-container-multi {
width: 395px!important;
}

#{$form->getId()} textarea {
height: 60px;
width: 395px;
}

#{$form->getId()} input[type=text] {
width: 395px;
}

#template, #diaporama {
width: 405px;
}
{/appendScript}
