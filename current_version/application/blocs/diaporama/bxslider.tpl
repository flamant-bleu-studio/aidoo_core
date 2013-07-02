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

{if $items|@count > 0}

	<div id="diaporama-{$datas.diaporamaId}"  class="diaporama bxslider">
		<ul>
		{foreach name=diaporama key=key from=$items item=diapo}
			<li class="diapo" id="diapo{$key+1}">
			
				{if $diapo['addLink']}
					<a class="link" href="{$diapo['url']}" {if $diapo['window']}target="_blank"{/if}>
				{/if}
					<img class="img" src="{$diapo['image']}" width="{$diaporamaWidth}px" height="{$diaporamaHeight}px" />
					
					{if $diapo['image2']}
						<img class="img2" src="{$diapo['image2']}" />
					{/if}
					
					<div class="caption">
						{$diapo["description"]}
					</div>
					
				{if $diapo['addLink']}
					</a>
				{/if}
					
			</li>
		{/foreach}
		</ul>
	</div>
	
{/if}

{appendFile type="css" src="{$smarty.const.COMMON_LIB_PATH}/lib/bxslider/jquery.bxslider.css"}
{appendFile type="js" src="{$smarty.const.COMMON_LIB_PATH}/lib/bxslider/jquery.bxslider.min.js"}
	
<script type="text/javascript">
	$('#diaporama-{$datas.diaporamaId}').find('ul').bxSlider({
		
		{if $datas.bx_type != 'ticker'}
			auto: true,
			autoHover: true,
			mode: '{$datas.bx_type}',
			displaySlideQty: {$datas.displaySlideQty},
			moveSlideQty: {$datas.moveSlideQty},
			
			
			{if $datas.pagination}
				pager: true,
			{/if}
		{else}
			ticker: true,
		{/if}
		{if $datas.pause}pause: {$datas.pause},{/if}
		controls: false
		
	});
</script>
