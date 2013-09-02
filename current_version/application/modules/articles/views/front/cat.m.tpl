<div id="view-cat">
	{if $articles}
		
		{foreach from=$articles key=key item=item}
			{if $item->readmore == 1}
				<a href="{routeShort action="view" id=$item->id_article}" rel="external">
			{/if}
				<div class='article'>
					<div class="content carre {cycle values="articlePair,articleImpair"}">
					
						<div style="text-align: center;">
							{if $item->image}
								<img src="{image folder='articles' name=$item->image size=$size}" />
							{/if}
						</div>
						
						<div>
							<h3 class="title">{$item->title}</h3>
							
							<p class="date">{formatDate format="EEE F à HH:mm" date=$item->date_start}</p>
						</div>
						
						<div class="clear"></div>
					</div>
				</div>
			{if $item->readmore == 1}
				</a>
			{/if}
		{/foreach}
		
	{else}
		<h3>{t}No news to display{/t}</h3>
	{/if}
	
</div>