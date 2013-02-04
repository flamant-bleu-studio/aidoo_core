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

<div class="bloc_advertising">
	
	{if $advert}
		
		{if $advert["link_type"] == 0}
			<a href='{$advert["page_link"]}' {if $advert["window"] == 1}target="_blank"{/if}>
				<img class="bloc_advertising_image" src="{$advert['image_path']}" />
			</a>
			{if $advert["addtext"]}
				<div class="bloc_advertising_caption">
					<div class="bloc_advertising_content">
						{$advert["text"]}
					</div>
				</div>
			{/if}
		{elseif $advert["link_type"] == 1}
			<a href="http://{$advert['external_page']}" {if $advert["window"] == 1}target="_blank"{/if}>
				<img class="bloc_advertising_image" src="{$advert['image_path']}" />
			</a>
			{if $advert["addtext"]}
				<div class="bloc_advertising_caption">
					<div class="bloc_advertising_content">
						{$advert["text"]}
					</div>
				</div>
			{/if}
		{else}
			<img class="bloc_advertising_image" src="{$advert['image_path']}" />
			{if $advert["addtext"]}
				<div class="bloc_advertising_caption">
					<div class="bloc_advertising_content">
						{$advert["text"]}
					</div>
				</div>
			{/if}
		{/if}
	
	{else if $error}
		{t}Advertising not found{/t}
	{/if}
	
</div>
