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

{if $url}
<a href="{$url}">
{/if}
<div class="mbloc {if $icon}hasIcon{else}hasNoIcon{/if} {if $text}hasText{else}hasNoText{/if}" style="background-color:#{$background_color};">
	
	{if $icon}
		<img src="http://{$smarty.server.SERVER_NAME}{$baseUrl}/skins/{$smarty.const.SKIN_FRONT}/icon/{$icon}.png" />
	{/if}
	
	{if $text}
		<div class="text" style="color:#{$text_color};">{$text}</div>
	{/if}
	
	<div class="clear"></div>
</div>
{if $url}
</a>
{/if}