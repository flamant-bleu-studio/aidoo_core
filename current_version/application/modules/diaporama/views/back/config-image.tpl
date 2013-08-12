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
			<li class="step complete">
				<a href="{routeShort action="add-image" id=$diaporama->id}">
					<span class="badge badge-success">2</span> {t}Import and order images{/t}
					<span class="chevron"></span>
				</a>
			</li>
			<li class="step active"><span class="badge badge-info">3</span> {t}Image configuration{/t}
				<span class="chevron"></span>
			</li>
		</ul>
	</div>
	
	<div id="infos" class="alert alert-info"><i class="icon-hand-up"></i> &nbsp;&nbsp;{t}Click an image to configure{/t}</div>
	
	<div id="images">
		
		{foreach from=$images item=image}
			
			<div class="image">
				<a href="{routeShort action="edit-image" id=$image->id}" class="iframe" title="{t}Config image{/t}">
					<img src="{image folder='diaporama' name=$image->image}" />
				</a>
			</div>
		
		{/foreach}
		
	</div>
	
</div>