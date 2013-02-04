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

<div class="users view">
	<div class="user_head">
		<div class="user_id left">
			<div class="ligne1">
				GIRL
			</div>
			<div class="ligne2">
				#{$user->id}
			</div>
		</div>
		<div class="name left">
				{$user->getPublicName()} 
		</div>
		<div class="move right">
			{if $prev_user}
				<span class="previous"><a href="{routeShort action='view' page=$prev_user->id}">< {t}Previous{/t}</a></span>
			{/if}
			{if $next_user}
				<span class="next"><a href="{routeShort action='view' page=$next_user->id}">{t}Next{/t} ></a></span>
			{/if}
		</div>
		<div class="clear"></div>
	</div>
	<div class="user_infos">
		
		{if $user->metas->images}
			<img src="{image folder='profils' name=$user->metas->images size='small'}" />
		{else}
		<p class="no_photo">Pas de photo à afficher pour {$user->getPublicName()}</p>
		{/if}
	
		{if !$user->metas->moto && !$user->metas->url}
			<div class="item">
				<p>Aucune information à afficher à propos de {$user->getPublicName()} </p>
			</div>
		{else}

			{if $user->metas->moto}
				<div class="item">
					<span class="label">Ma moto :</span> {$user->metas->moto}
				</div>
			{/if}
							
			{if $user->metas->url}
				<div class="item">
					<span class="label">Où me trouver  :</span> <a href="{if $user->metas->url|substr:0:5 != "http://"}http://{/if}{$user->metas->url}" target="_blank">{$user->metas->url}</a>
				</div>	
			{/if}
			
			{if $user->metas->birthday}			
				<div class="item">				
					<span class="label">Mon anniversaire :</span> {$user->metas->birthday}
				</div>
			{/if}
			
			{if $user->metas->biographie}
				<div class="item">
					<span class="label">Moi, en quelques mots :</span> {$user->metas->biographie}
				</div>
			{/if}			
		{/if}
		
		{if $articles}
		
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId={$smarty.const.FACEBOOK_APPID}";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			
			<script>
			!function(d,s,id){
				var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){
						js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
					}
				}(document,"script","twitter-wjs");
			</script>
			<div class="user_conseils">
				<h3>Mes Articles</h3>
				<div class="list_article" id="rub_{$idCategorie}">
				
					{foreach from=$articles key=key item=item}
					<div class='article {cycle values="articlePair,articleImpair"}'>
			
						<h2 class="title">
							<a class="read_more" href="{routeFull route="articles" action="view" id=$item->id_article}" title="Consulter l'article sur {$item->title}">
								{$item->title}
							</a>
						</h2>
						
						<div class="date_article">
							{formatDate format="EEE F à HH:mm" date=$item->date_start}
						</div>
						
						<a href="{routeFull route="articles" action="view" id=$item->id_article}" title="Consulter {$item->title}"><div class="image"><img src="{image folder='articles' name=$item->image size='small'}" /></div></a>
						
						<a href="{routeFull route="articles" action="view" id=$item->id_article}" title="Consulter {$item->title}"><div class="chapeau">{$item->chapeau}</div></a>
						
						<div style="clear:both;"></div>
						
						<div class="footer_article">
			
							<div class="categories">
								{foreach from=$item->categories item=cat}
									<a href="{routeFull route="articles" action="cat" id=$cat->id_categorie}">{$cat->title}</a>
								{/foreach}
								
							</div>
							
							{if $item->fb_comments_active}
								<div class="nb_com_fb">
									<fb:comments-count href="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}" /></fb:comments-count> {t}comment{/t}(s)
								</div>
							{/if}
														
							{if $item->readmore == 1}
							<div class="read_more">
								<a title="Lire la suite" href="{routeFull route="articles" action="view" id=$item->id_article}">{t}Read more{/t}</a>
							</div>
							{/if}
							
							<div class="share">
								<a href="https://twitter.com/share" class="twitter-share-button" data-count="none"
									data-url="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}"></a>
							</div>
							
							<div class="share">
								<div class="fb-like" data-href="http://{$smarty.server.SERVER_NAME}/articles/view/{$item->id_article}" data-send="false" data-layout="button_count" data-show-faces="false"></div>
							</div>
							
							<div class="clear"></div>	
						</div>
							
						
						<div class="clear"></div>
					</div>
					{/foreach}
				</div>
			</div>
		{/if}
	</div>
	
</div>
