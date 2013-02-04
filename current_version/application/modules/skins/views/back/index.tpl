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

<div class="content_titre">
	<h1>{t}Skins Manager{/t}</h1>
	<div>{t}Skins Manager{/t}</div>
</div>	
	
<div class="zone">
	<div class="zone_titre">
		<h2>{t}Skins Manager{/t} <span class="helper"></span></h2>
		<div>{t}Skins Manager{/t}</div>
	</div>

	<form action="#">
	<fieldset>
	<div>
		<table class="table table-bordered table-striped" width="100%">
			<tr>
				<th><a href="#" class="desc">{t}Name{/t}</a></th>
				<th><a href="#" class="desc">ScreenShot</a></th>
				<th><a href="#">{t}FrontOffice Skin{/t}</a></th>
				<th><a href="#">{t}BackOffice Skin{/t}</a></th>
				<th><a href="#">{t}Supported Devices{/t}</a></th>
			</tr>

			{if $skins != null}
					 {foreach from=$skins item=skin}
					 
						<tr class='{cycle values="second,first"}'>
							<td><span class="skintitle">{$skin.name}</span></td>
							<td>
								{if $skin.skintype == "frontofficeskin"}
								<img src="{$baseUrl}/skins/{$skin.path}/screenshot.jpg" />
								{else}
								<img src="{$adminUrl}/{$skin.path}/screenshot.jpg" />
								{/if}
							</td>
							<td>
							{if $skin.path == $defaultSkinFront}
								<span class="system positive">{t}Active{/t}</span>
							{else}
								<span>
								{if $skin.skintype == "frontofficeskin"} 
								<a href="{routeShort action="setdefaultfront" name=$skin.path}">{t}Activate{/t}</a>
								{/if}
								</span>
							{/if}
							</td>
							<td>
							{if $skin.path == $defaultSkinBack}
								<span class="system positive">{t}Active{/t}</span>
							{else}
								<span>
								{if $skin.skintype == "backofficeskin"}
								<a href="{routeShort action="setdefaultback" name=$skin.path}">{t}Activate{/t}</a>
								{/if}
								</span>
							{/if}
							</td>
							
							<td>
								<span>
								[{t}Standard Browser{/t}]
								{foreach from=$skin.handsets.handset item=set}
								[{$set.subskin}]
								{/foreach}
								</span>
							</td>
							
							
						</tr>
					
					 {/foreach}
				
			
			{else}
						<tr class='second'>
							<td colspan="6" style="text-align: center;">{t}You do not have any skin yet. Click Add !{/t}</td>
						</tr>
			{/if}
			
		</table>
	</div>
	</fieldset>
	</form>
</div>


{if $backAcl->hasPermission("mod_skins", "manage")}

	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>Choisissez vos options</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
		</div>
		<div class="droits_content">
			
			<form action="{$formAcl->getAction()}" method="post"> 
				{$formAcl}
				<ul class="unstyled">
					<li><span class="bleu">Manage :</span> éditer les droits</li>
					<li><span class="bleu">View :</span> voir le module et son contenu</li>
					<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
				</ul>
				<div class="droits_submit">
					{$formAcl->submit}
				</div>
			</form> 
			
		</div>
	</div>

{/if}	
