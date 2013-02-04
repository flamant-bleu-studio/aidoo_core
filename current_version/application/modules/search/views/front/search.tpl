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

<div id="search_results">
	
	<h1 class="title">{t}Result of your search{/t} "<span class="keywords italic">{$keywords}</span>"</h1>
	
	{if $results != null}
		
		{foreach from=$results item=result}
		
			<div class="search_result">
				
				<h2 class="result_title"><a href='{$baseUrl}{$result.url_front}'>{$result.title}</a></h2>
				
				{if $result.picture}
					<div class="image">
						<img src="{image folder=$result.picture_folder name=$result.picture size=$options['imageFormat']}" />
					</div>
				{/if}
				
				{if $result.description}
					<p class="description">
						{$result.description} 
					</p>
				{/if}
				
				<p class="more">
					<a href='{$baseUrl}{$result.url_front}'>{t}Read more{/t}</a>
				</p>
			</div>
			
		{/foreach}
		
	{else}
		<h2>{t}No results{/t}</h2>
	{/if}
	
	{$pagination}
	
</div>
