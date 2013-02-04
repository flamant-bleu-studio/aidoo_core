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

{if $articles}

	{* FB Like button + FB comment *}
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId={$smarty.const.FACEBOOK_APPID}";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	{* Twitter Button *}
	<script>
	!function(d,s,id){
		var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){
				js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
			}
		}(document,"script","twitter-wjs");
	</script>
	
	<div class="list_article" id="rub_{$idCategorie}">
		{foreach from=$articles key=key item=item}
		<div class='article {cycle values="articlePair,articleImpair"}'>
 			
			{if $item->readmore == 1}
				<a class="read_more" href="{routeShort action="view" id=$item->id_article}" title="Consulter {$item->title}">
			{/if}
			
				<h2 class="title">{$item->title}</h2>
				
			{if $item->readmore == 1}
				</a>
			{/if}
			
			<div class="date_article">
				{formatDate format="EEE F à HH:mm" date=$item->date_start}
				{if $authors[$item->author]}
					par <a href="{routeFull route="users" action="view" page=$item->author}">{$authors[$item->author]->getPublicName()}</a>
				{/if}
			</div>
			
			{if $item->readmore == 1}
			<a href="{routeShort action="view" id=$item->id_article}" title="Consulter {$item->title}">
			{/if}
			{if $item->image}
				<div class="image">
					<img src="{image folder='articles' name=$item->image size=$size}" />
				</div>
			{/if}
			{if $item->readmore == 1}
			</a>
			{/if}
			
			{if $item->readmore == 1}
			<a href="{routeShort action="view" id=$item->id_article}" title="Consulter {$item->title}">
			{/if}
				<div class="chapeau">{$item->chapeau}</div>
			{if $item->readmore == 1}
			</a>
			{/if}
			
			<div style="clear:both;"></div>
			
			<div class="footer_article">

				<div class="categories">
					{foreach from=$item->categories item=cat}
						<a href="{routeShort action="cat" id=$cat->id_categorie}">{$cat->title}</a>
					{/foreach}
					
				</div>
				
				{if $fb_comments_number_show && $item->readmore == 1}
					<div class="nb_com_fb">
						<a href="{routeShort action="view" id=$item->id_article}#article_comments">
						<fb:comments-count href="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}" /></fb:comments-count> {t}comment{/t}(s)</a>
					</div>
				{/if}
				
				{if $item->readmore == 1}
					<div class="read_more">
						<a title="Lire la suite" href="{routeShort action="view" id=$item->id_article}">{t}Read more{/t}</a>
					</div>
				
					<div class="share">
						<a href="https://twitter.com/share" class="twitter-share-button"
							data-url="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}"></a>							
					</div>
					<div class="share">
						<div class="fb-like" data-href="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}" data-send="false" data-layout="button_count" data-show-faces="false"></div>
					</div>
				{/if}

				<div class="clear"></div>	
			</div>
			
			<div class="clear"></div>
		</div>
		{/foreach}
	</div>
	
	<div class="paginator_list_articles">{$pagination}</div>
{else}
	<h3>{t}No news to display{/t}</h3>
{/if}

<script type="text/javascript">
FB.XFBML.parse(document.getElementById("content"));
</script>
