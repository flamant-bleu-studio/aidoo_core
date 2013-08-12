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
			<li class="step complete">
				<a href="{routeShort action="edit" id=$diaporama->id}">
					<span class="badge badge-success">1</span> {t}Configuration slideshow{/t}
					<span class="chevron"></span>
				</a>
			</li>
			<li class="step active">
				<span class="badge badge-info">2</span> {t}Import and order images{/t}
				<span class="chevron"></span>
			</li>
			<li class="step {if !empty($images)}complete{/if}">
				{if !empty($images)}<a href="{routeShort action="config-image" id=$diaporama->id}">{/if}
					<span class="badge {if !empty($images)}badge-success{/if}">3</span> {t}Image configuration{/t}
					<span class="chevron"></span>
				{if !empty($images)}</a>{/if}
			</li>
		</ul>
	</div>
	
	<div id="infos" class="alert alert-info"><i class="icon-move"></i> &nbsp;&nbsp;{t}Drag & Drop images to order{/t}. {t}Do not forget to save{/t}.</div>
	
	<form method="POST">
		
		{$form->images}
		{$form->save}
		
	</form>
	
</div>