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

<div class="users list">

	{foreach from=$users item=user}
		<a href="{routeShort action="view" page=$user->id}" class="user" style="float: left; margin: 10px;">
			<div class="divmembre">
				{if $user->metas->images}
					<img src="{image folder='profils' name=$user->metas->images size='small'}" />
				{/if}
				<div class="nom_membre">{$user->getPublicName()}</div>
			</div>
		</a>
	{/foreach}
	
	<div class="clear"></div>
	
	{$pagination}
</div>
