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

{include file="{$smarty.const.APPLICATION_PATH}/modules/admin/views/cache/menu.tpl" active="index"}

<div id="content">
	
	<a href='{routeShort controller="cache" action="clear-assets"}' class="btn btn-primary">{t}Clear assets cache{/t}</a>
	<a href='{routeShort controller="cache" action="clear-templates"}' class="btn btn-primary">{t}Clear templates cache{/t} </a>
	<a href='{routeShort controller="cache" action="clear-cms"}' class="btn btn-primary">{t}Clear CMS cache{/t} </a>
	
	<br /> <br />
	
	<a href='{routeShort controller="cache" action="clear-all"}' class="btn btn-warning">{t}Clear all cache{/t} </a>
</div>