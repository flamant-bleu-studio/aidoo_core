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

{$form->service}
{$form->apiKey}
{$form->getDirections}
{$form->mode}

<div id="display_byform" {if $form->mode->getValue() != "byForm"}style="display:none;"{/if} >
	{$form->latitude}
	{$form->longitude}
</div>

{$form->zoom}
{$form->mapWidth}
{$form->mapHeight}
	
{literal}
<script type="text/javascript">
	$("input[name=mode]").on("click", function(){
		$("#display_byform").toggle($(this).val() == "byForm");
	});
</script>
{/literal}
