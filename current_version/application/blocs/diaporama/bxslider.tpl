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

	<div id="diaporama-{$datas.diaporamaId}"  class="diaporama diapo_classic">
		<ul>
		{foreach name=diaporama key=key from=$items item=diapo}
			{if $diapo->isActive()}
				<li class="diapo" id="diapo{$key+1}">
					
					{if $diapo->link_type != 0}
						<a class="link" href="$diapo->getUrl()" {if $diapo->link_target_blank}target="_blank"{/if}>
					{/if}
						<img src="{image folder='diaporama' name=$diapo->image}" />
						
						<div class="caption">
							{$diapo->text}
						</div>
						
					{if $diapo->link_type != 0}
						</a>
					{/if}
						
				</li>
			{/if}
		{/foreach}
		</ul>
	</div>
	
{/if}

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
