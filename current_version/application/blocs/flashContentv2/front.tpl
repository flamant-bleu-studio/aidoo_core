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

{appendFile type="js" src="/lib/swfobject/swfobject.js"}

{literal}
	<script type="text/javascript">
	var flashvars = {};
	{/literal}{$content}{literal}
    var attributes = {};
    attributes.wmode = "transparent";
   	attributes.quality ="high";
	attributes.swfversion ="6.0.65.0";

    swfobject.embedSWF({/literal}"{$swf}"{literal},
        // Here you can set ID for the wrapper div
        {/literal}"swfobject-{$id}"{literal},
        // Here you can set the with for the flash application
        {/literal}"{$width}"{literal}, {/literal}"{$height}"{literal}, {/literal}"{$version}"{literal},
        {/literal}"{$baseUrl}/lib/swfobject/expressInstall.swf"{literal},
        flashvars, attributes);
	</script>
{/literal}

<div id="swfobject-{$id}">
	<a href="http://www.adobe.com/go/getflashplayer">
		<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
	</a>
</div>
