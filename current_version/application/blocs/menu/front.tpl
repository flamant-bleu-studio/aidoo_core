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

{assign var='counterFunction' value=0} <!-- var level current (default : 0) -->

<div id="menu-{$idMenu}-bloc-{$id}" class="{if $desactiveDeroulant}fixe{else}deroule{/if} {if $align == 0}horizontal{else}vertical{/if}">
{if $items|@count > 0}
	{call name=generateUlLi liste=$items}
{/if}
</div>

{function name=generateUlLi}
	
	{$counterFunction=$counterFunction+1} <!-- up current level -->
	
	<ul class="{if $counterFunction > 1}sublevel {/if}level-{$counterFunction}">
		
		{assign var='counterIndex' value=1}
		
		{foreach name=menu key=key from=$liste item=item}
			
			{* Si uniquement les dossiers du premier niveau : *}
			{if !$displayOnlyFolder or ($displayOnlyFolder && ($counterFunction == 1 && ($item->type == 4 or $item->type == 5 or $item->type == 6 or $item->type == 7))) or ($displayOnlyFolder && $counterFunction != 1)}
				
				{assign var="classes" value=""}
				{if $item->loadAjax} {assign var="classes" value=$classes|cat:"fragment "}{/if}
				{if $item->id_menu|in_array:$activePageId} {assign var="classes" value=$classes|cat:"active "} {/if}
				
				<li id="menu-item-{$item->id_menu}" class="{$item->cssClass} {if $item->children}hasSublevels{/if} {if $item->id_menu|in_array:$activePageId}active{/if} {if $smarty.foreach.menu.first && $smarty.foreach.menu.last}single{elseif $smarty.foreach.menu.first}first{elseif $smarty.foreach.menu.last}last{/if} idx-{$counterIndex}">
					
					{if !$smarty.foreach.menu.first && $counterFunction == 1}
						<span class="separator">
							{if $separator}{$separator}{/if}
						</span>
					{/if}
				
					{if $counterFunction == 1} <!-- level 1 -->
						{if $item->hidetitle == 1}
							<a class="{$classes}"	
								{if $item->loadAjax} data-type="content" {/if}							 
								href="{$item->getLink()}" 
								{if $item->tblank}target='_blank'{/if} >
								<span class="icon_menu"><img src="{$item->image}" /></span>
								<span class="subTitle">{$item->subtitle}</span>
							</a>
						{else}
							{if $item->type == 3 || $item->type == 4}
								<span class="{if $item->id_menu|in_array:$activePageId}active{/if} nolink">
									<span class="icon_menu">{if $item->image}<img src="{$item->image}" /> {/if}{$item->label}</span>
									<span class="subTitle">{$item->subtitle}</span>
								</span>
							{else}
								<a class="{$classes}" 
									{if $item->loadAjax} data-type="content" {/if} 
									href="{$item->getLink()}" {if $item->tblank}target='_blank'{/if}>
									<span class="icon_menu">{if $item->image}<img src="{$item->image}" /> {/if}{$item->label}</span>
									<span class="subTitle">{$item->subtitle}</span>
								</a>
							{/if}
						{/if}
					{else}
						{if $item->type == 3 || $item->type == 4}
							<span class="icon_menu">{if $item->image}<img src="{$item->image}" /> {/if}{$item->label}</span>
							<span class="subTitle">{$item->subtitle}</span>
						{else}
							<a class="{$classes}"
								{if $item->loadAjax} data-type="content" {/if} 
								href="{$item->getLink()}" {if $item->tblank}target='_blank'{/if}>
								{$item->label}
							</a>
						{/if}
					{/if}
					
					{if $counterFunction < $levelDisplay || $levelDisplay == 0} <!-- $levelDisplay == 0 : unlimited -->
						{if $item->children}
							{if $counterFunction > 1} <!-- It's not level 1 -->
								<span class="arrow"></span>
							{/if}
							{call name=generateUlLi liste=$item->children} <!-- recursive function -->
						{/if}
					{/if}
					
				</li>
				
			{/if}
			
			{$counterIndex=$counterIndex+1}
		{/foreach}
	</ul>
	
{/function}


{if !$desactiveDeroulant}
{literal}
<script type="text/javascript">
$(document).ready(function() {

	(function($) {
		
		$.menu = function(element, options) {
		
			// Defaults options
			var defaults = {
				classHover : "hover",
				align : "horizontal"
			}
			
			var plugin = this;
			plugin.opts = {}
			
			var $element = $(element),  // reference to the jQuery version of DOM element the plugin is attached to
				element = element;     // reference to the actual DOM element
			
			plugin.init = function() {
				plugin.opts = $.extend({}, defaults, options);
				
				$element.find("li").hover(function(){
					$(this).addClass(plugin.opts.classHover);
					if( plugin.opts.align == "Vertical" )
						$(this).find("ul:first").css("display", "block");
					$('ul:first',this).css('visibility', 'visible');
				}, function(){
					$(this).removeClass(plugin.opts.classHover);
					if( plugin.opts.align == "Vertical" )
						$(this).find("ul:first").css("display", "none");
					$('ul:first',this).css('visibility', 'hidden');
				});
			}
			
			plugin.init();
		}
		
		$.fn.menu = function(options) {
			return this.each(function() {
				if (undefined == $(this).data('menu')) {
					var plugin = new $.menu(this, options);
					$(this).data('menu', plugin);
				}
			});
		}
		
	})(jQuery);

	$("#menu-{/literal}{$idMenu}{literal}-bloc-{/literal}{$id}{literal} .level-1").menu({"align":"{/literal}{$align}{literal}"}); // level 1 ul attach
});
</script>
{/literal}
{/if}
