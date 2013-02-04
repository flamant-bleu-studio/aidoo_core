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
	<h1>{t}Module article{/t}</h1>
	<div>{t}Manage options{/t}</div>
</div>
	
<div class="zone">
	<form action="{$form->getAction()}" method="post" id="{$form->getId()}"> 
	
		<div class="zone_titre">
			<h2>{t}General{/t}</h2>
		</div>
		
		{$form->imageFormat}
		{$form->authorInArticle}
		
		{$form->notifyNewArticle}
		<div id="show_emailNotifyNewArticle">
			{$form->emailNotifyNewArticle}
		</div>
		
		{$form->notifyValidateArticle}
			
		<div class="zone_titre">
			<h2>{t}Pagination{/t}</h2>
		</div>
		{$form->ajaxEnable}
		{$form->ajaxNoScrollTop}
		{$form->ajaxEffect}
				
		<div class="zone_titre">
			<h2>{t}Facebook comments{/t}</h2>
		</div>
		
		{$form->fb_comments_active_default}
		{$form->fb_comments_active}
		
		<div id="show_facebookCommentsOptions">
			{$form->fb_comments_width}
			{$form->fb_comments_number}
			{$form->fb_comments_color}
		</div>
		
		{if $backAcl->hasPermission("mod_articles", "manage")}
		<div class="zone_titre">
			<h2>{t}Generate rewrite{/t}</h2>
		</div>
		<a href='{routeShort action="create-rewrite"}' class="btn btn-danger btn-large">{t}Create rewrite{/t}</a>
		{/if}
		
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

<script type="text/javascript">

$("#show_facebookCommentsOptions").toggle($("#fb_comments_active").is(":checked"));
$("#fb_comments_active").on('click', function(){
	$("#show_facebookCommentsOptions").toggle($(this).is(":checked"));
});

$("#show_emailNotifyNewArticle").toggle($("#notifyNewArticle").is(":checked"));
$("#notifyNewArticle").on('click', function(){
	$("#show_emailNotifyNewArticle").toggle($(this).is(":checked"));
});
</script>
