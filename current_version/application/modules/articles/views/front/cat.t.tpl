<div id="view-cat">
	{if $articles}
		
		<ul class="list_article ui-listview" id="rub_{$idCategorie}" data-role="listview" data-split-theme="d">
			
			{foreach from=$articles key=key item=item}
				<li class='article {cycle values="articlePair,articleImpair"}'>
					{if $item->readmore == 1}
						<a href="{routeShort action="view" id=$item->id_article}" rel="external">
					{/if}
					
					{if $item->image}
						<img src="{$imagePath}{$item->image}" />
					{/if}
					
					<h3 class="title">{$item->title}</h3>
					
					<p class="date">{formatDate format="EEE F à HH:mm" date=$item->date_start}</p>
					{if $item->chapeau}
						<p class="chapeau">{$item->chapeau}</p>
					{/if}
					
					{if $item->readmore == 1}
						</a>
					{/if}
				</li>
			{/foreach}
			
		</ul>
	{else}
		<h3>{t}No news to display{/t}</h3>
	{/if}
	
	<br/><br/>
	
	{if $pagination}
		<div id="paginator">{$pagination}</div>
	{/if}
</div>