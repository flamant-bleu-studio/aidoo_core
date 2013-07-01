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

<div class="faq">
	
	{if $faqs->intro}
		<div class="intro">{$faqs->intro}</div>
	{/if}
	
	{if $faqs->nodes}
		<div id="faq">
		{foreach $faqs->nodes as $faq}
			<h2>{$faq->question}</h2>
			<div>
				{$faq->answer}
			</div>
		{/foreach}
		</div>
	{else}
		Aucune question/réponse ...
	{/if}
	
	{if $faqs->outro}
		<div class="outro">{$faqs->outro}</div>
	{/if}
	
</div>
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$("#faq").accordion({autoHeight: false});
});
{/literal}
</script>
