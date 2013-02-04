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
	<h1>{t}Categories{/t}</h1>
	<div>{t}Manage options{/t}</div>
</div>

<div class="zone">
	
	<form action="{$form->getAction()}" method="post" id="{$form->getId()}"> 
		
		<div class="zone_titre">
			<h2>{t}General{/t}</h2>
		</div>
		
			{$form->imageFormat}
			
			{$form->authorInArticle}
			
			{$form->fb_comments_number_show}
		
		<div class="zone_titre">
			<h2>{t}Manage rights{/t}</h2>
		</div>
		
			{$formAcl}
		
		<ul class="unstyled">
			<li><span class="bleu">Manage :</span> éditer les droits</li>
			<li><span class="bleu">View :</span> voir le module et son contenu</li>
			<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
		</ul>
		
		<div class="form_submit">
			<button class="btn btn-large btn-success">{t}Submit{/t}</button>
		</div>
	</form> 

</div>
