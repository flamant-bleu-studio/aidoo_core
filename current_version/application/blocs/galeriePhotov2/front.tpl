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

{if $items|@count > 0}
	{appendFile src="{$smarty.const.COMMON_LIB_PATH}/lib/fancybox/fancybox.css" type="css"}
    {appendFile src="{$smarty.const.COMMON_LIB_PATH}/lib/fancybox/jquery.fancybox-1.3.4.pack.js" type="js"}
<div id="gallery" {if $diaporamaWidth}style="width:{$diaporamaWidth}px;height:{$diaporamaHeight}px;"{/if}>
    {foreach name=diaporama key=key from=$items item=diapo}
        <div class='thumb' id="gallery{$key+1}" >
            <a class='iframe'
               href="{$diapo['image']}"
               rel='gallery'>
                <img src="{$diapo['thumb']}" alt='Concrete' class='lbThumb'/>
            </a>
            <br />
            <div class="content">
                {$diapo["description"]}
            </div>
        </div>
    {/foreach}
</div>
{/if}

{function name=generatePagination}
	<div class="pagination">
		{section name=foo start=1 loop=($items|@count)+1}
			{if $smarty.section.foo.index neq $current_index}
				<a href="#" class="page">{$smarty.section.foo.index}</a>
			{else}
				<span class="page current">{$smarty.section.foo.index}</span>
			{/if}
		{/section}
	</div>
{/function}
<script type="text/javascript">
     $(document).ready(function() {

	$("a.iframe").fancybox({
		centerOnScroll: true,
     });

	});
</script>
