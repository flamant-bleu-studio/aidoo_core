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
	{foreach from=$articles key=key item=item}
	
	<div class='blogPost {cycle values="blogPostOdd,blogPostEven"}'>
		<div class="blogPostHeader">
			<h1>{$item->title}</h1>
		</div>
		<div class="blogPost blogPostSummary">
			<p>
				{$item->chapeau}
			</p>
			
			{if $item->readmore}
			<p><a href="{routeFull route="articles" action="view" id=$item->id_article}">{t}Read more{/t}...</a></p>
			{/if}
		</div>
	</div>
	
	{/foreach}
{else}
	{t}No items{/t}
{/if}
