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

{include file="{$smarty.const.APPLICATION_PATH}/modules/diaporama/views/back/menu.tpl" active="edit"}

<div id="content">
	
	<div id="steps">
		<ul>
			<li class="step active">
				<span class="badge badge-info">1</span> {t}Configuration slideshow{/t}
				<span class="chevron"></span>
			</li>
			<li class="step {if !empty($images)}complete{/if}">
				<a href="{routeShort action="add-image" id=$diaporama->id}">
					<span class="badge {if !empty($images)}badge-success{/if}">2</span> {t}Import and order images{/t}
					<span class="chevron"></span>
				</a>
			</li>
			<li class="step {if !empty($images)}complete{/if}">
				{if !empty($images)}<a href="{routeShort action="config-image" id=$diaporama->id}">{/if}
					<span class="badge {if !empty($images)}badge-success{/if}">3</span> {t}Image configuration{/t}
					<span class="chevron"></span>
				{if !empty($images)}</a>{/if}
			</li>
		</ul>
	</div>
	
	<form action='{$form->getAction()}' method="post" id="{$form->getId()}"> 
		
		{$form->title}
		{$form->size}
		
		{if $backAcl->hasPermission('mod_diaporama', 'manage')}
			<div class="zone">
			
				<h3>{t}Rights{/t}</h3>
			
				<div class="droits_content">
					{$formAcl}
				</div>
			</div>
		{/if}
		
		{$form->save}
		
	</form>
	
</div>