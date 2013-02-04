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

<div id="bloc-membres-{$id}" class="{if $datas.displayMode != "0"}scroll-{if $datas.scrolling == "0"}hori{else}verti{/if}{else}no-scroll{/if} align-{if $datas.alignment == "0"}hori{else}verti{/if}">
	{if $count > 0}
		<ul>
			{section start=0 loop=$countli name=bouclea}
				<li>	
					{section start=0 loop=$countbyli name=boucleb}
					
						{assign var=a value=$membres[$smarty.section.bouclea.index+$smarty.section.boucleb.index]}
						
						{if $datas.showArchive}
							<a href="{routeFull route="users" action="list"}" class="linkArchives">
								{$datas.textArchive}
							</a>
							<div class="clear"></div>
						{/if}
		
						
						<a class="membre" href="{routeFull route="users" action="view" page=$a->id}">
							{if $a->metas->images}
								<div class="image">
									<img src="{$imagePath}{$a->metas->images}" alt="{$a->firstname}" title="{$a->firstname}" />
								</div>
							{/if}
	
							<div class="nom">
								{$a->firstname}
							</div>

						</a>
						
					{/section}
					<div class="clear"></div>
				</li>
			{/section}
		</ul>
	{else}
	<h3>{t}No member to display{/t}</h3>
	{/if}
</div>
	
	
{if $datas.displayMode == "1" || $datas.displayMode == "2"}

	{appendFile type="css" src="/lib/bxslider/bx_styles/bx_styles.css"}
	{appendFile type="js" src="/lib/bxslider/jquery.bxSlider.min.js"}
	
<script type="text/javascript">
	$("#bloc-membres-{$id}").find("ul").bxSlider({
		mode: {if $datas.scrolling == "0"}"horizontal"{else}"vertical"{/if},
		displaySlideQty: {$displaySlideQty},
		moveSlideQty: {$displaySlideQty},
		prevText: '',
		nextText: '',
		infiniteLoop: true,
		controls: {if $datas.showArrow}true{else}false{/if},
		{if $ticker == true}
			ticker: true,
			tickerSpeed: {$datas.tickerSpeed},
			{if $datas.stopHover}tickerHover: true,{/if}
		{else}
			{if $datas.autoStart}auto: true,{/if}
			{if $datas.stopHover}autoHover: true,{/if}
				{if $datas.showPagination}
					pager: true,
					pagerLocation: "{$datas.pagerPosition}"
				{/if}
		{/if}
	}); 
</script>
{/if}
	
	
