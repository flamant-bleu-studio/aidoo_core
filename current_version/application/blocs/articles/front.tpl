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

<div id="bloc-articles-{$id}" class="{if $datas.displayMode != "0"}scroll-{if $datas.scrolling == "0"}hori{else}verti{/if}{else}no-scroll{/if} align-{if $datas.alignment == "0"}hori{else}verti{/if}">
	{if $count > 0}
		<ul>
			{section start=0 loop=$countli name=bouclea}
				<li>	
					{section start=0 loop=$countbyli name=boucleb}
					
						{assign var=a value=$articles[$smarty.section.bouclea.index+$smarty.section.boucleb.index]}
						
						<div class="article">
							{if $a->image}
								<div class="image">
									<img src="{image folder='articles' name=$a->image size=$imageFormat}" alt="{$a->title}" title="{$a->title}" />
								</div>
							{/if}
							
							<div class="content">
								<h3 class="title">{$a->title}</h3>
							
								{if $datas.showDate}
									<span class="date">{formatDate date=$a->date_start format=$datas.dateFormat}</span>
								{/if}
								
								<div class="chapeau">
									{if $datas.truncateText != "0"}
										{$a->chapeau|truncate:$datas.truncateText}
									{else}
										{$a->chapeau|truncate}
									{/if}
								</div>

								{if $a->readmore}
									<a class="readmore" href="{routeFull route="articles" action="view" id=$a->id_article}">{t}Read more{/t}</a>
									<div class="clear"></div>
								{/if}
							</div>
						</div>
						
					{/section}
					<div class="clear"></div>
				</li>
			{/section}
		</ul>
		
		{if $datas.showArchive}
			<a href="{routeFull route="articles" action="cat" id=$datas.category}" class="linkArchives">{$datas.textArchive}</a><div class="clear"></div>
		{/if}
	
	{else}
	<h3>{t}No news to display{/t}</h3>
	{/if}
</div>
	
	
{if $datas.displayMode == "1" || $datas.displayMode == "2"}

	{appendFile type="css" src="/lib/bxslider/bx_styles/bx_styles.css"}
	{appendFile type="js" src="/lib/bxslider/jquery.bxSlider.min.js"}
	
<script type="text/javascript">
	$("#bloc-articles-{$id}").find("ul").bxSlider({
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
	
	
