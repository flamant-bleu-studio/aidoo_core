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

{appendFile type="css" src="/lib/fancybox/fancybox.css"}
{appendFile type="js" src="/lib/fancybox/jquery.fancybox-1.3.4.pack.js"}

<div class="liste_photo" id="mozaique_photo">
	{foreach from=$galerie->nodes key=key item=image}
		<a href="{$image->path}" rel="grouped_elements" class="image">
			<img src="{$image->path_thumb}" data-color="#{$image->bg_color}"/>
			<div class="description">{$image->description}</div>
		</a>
	{/foreach}
</div>

<script type="text/javascript">
if(jQuery().fancybox) {

	$('#mozaique_photo a[rel=grouped_elements]').fancybox({
		'hideOnContentClick': true,
		'opacity'		: true,
		'overlayShow'	: false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
}
</script>
