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

<div class="bloc_langChooser">
	
	{if $url|@count > 0}
		{foreach name=lang from=$url key=code_lang item=url_lang}
			
			<a class="lang_{$code_lang}" href="{$smarty.const.BASE_URL}{$url_lang}">
				<img class="{if $smarty.const.CURRENT_LANG_CODE == $code_lang}active{/if}" src="{$smarty.const.BASE_URL}/images/flags/{$code_lang}.png" />
			</a>
			
		{/foreach}
	{/if}
	
</div>
