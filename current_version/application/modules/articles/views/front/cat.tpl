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
	
	
	<div class="list_article" id="cat_{$category->id_categorie}">
	
		<h2>{$category->title}</h2>
		
		<div class="desc-category">{$category->description}</div>
		
		{foreach from=$articles key=key item=item}
			<div class="article">
				
				<div class="image">
					{if $item->readmore == 1}<a href="{routeShort action="view" id=$item->id_article}" title="{$item->title}">{/if}
					
						{if $item->image}
							<img src="{image folder='articles' name=$item->image size=$size}" />
						{else}
							<img src="{$baseUrl}{$skinUrl}/images/no-img.png" />
						{/if}
						
					{if $item->readmore == 1}</a>{/if}
				</div>
				
					
					
				<div class="content">
					
					{if $item->readmore == 1}
						<h3><a href="{routeShort action="view" id=$item->id_article}" title="{$item->title}">{$item->title}</a></h3>
					{else}
						<h3>{$item->title}</h3>
					{/if}
					
					
					<div class="chapeau">{$item->chapeau}</div>
					
					<div class="read_more">
						<a title="Lire la suite" href="{routeShort action="view" id=$item->id_article}">{t}Read more{/t}</a>
					</div>
					
					<div class="categories">
						{foreach from=$item->categories item=cat}
							<a href="{routeShort action="cat" id=$cat->id_categorie}">{$cat->title}</a>
						{/foreach}
					</div>
					
					{if $authors[$item->author]}
						<div class="author">par <a href="{routeFull route="users" action="view" page=$item->author}">{$authors[$item->author]->getPublicName()}</a></div>
					{/if}
					
					{if $fb_comments_number_show && $item->readmore == 1}
						<div class="nb_com_fb">
							<a href="{routeShort action="view" id=$item->id_article}#article_comments">
							<fb:comments-count href="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}" /></fb:comments-count> {t}comment{/t}(s)</a>
						</div>
					{/if}
	
				</div>
				
			</div>
		{/foreach}
	</div>
	
	<div class="paginator_list_articles">{$pagination}</div>
{else}
	<p>{t}No news to display{/t}</p>
{/if}

<script type="text/javascript">
FB.XFBML.parse(document.getElementById("content"));
</script>
