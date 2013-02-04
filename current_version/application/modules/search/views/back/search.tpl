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
				
				<h2 class="result_title">{$result.title}</h2>
				
				{if $result.typeName}
					<span class="label">
						{$result.typeName}
					</span>
				{/if}
				
				{if !empty($result.url_front)}
					{if $result.isVisible}
						&nbsp;<span class="label label-success">{t}On{/t}</span>
					{else}
						&nbsp;<span class="label label-important">{t}Off{/t}</span>
					{/if}
				{/if}
				
				<br />
				
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
					<a href='{$baseUrl}{$result.url_back}'>{t}Edit{/t}</a>
					{if $result.isVisible && $result.url_front}- <a href='{$baseUrl}{$result.url_front}' target="_blank">{t}View{/t}</a>{/if}
				</p>
			</div>
			
			<div class="clearfix"></div>
			
			<hr />
			
		{/foreach}
	{else}
		<h2>{t}No results{/t}</h2>
	{/if}
	
	{$pagination}
	
</div>
