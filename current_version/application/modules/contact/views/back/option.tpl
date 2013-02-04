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

<div class="content_titre">
	<h1>{t}Options{/t}</h1>
</div>

<div class="zone">

{if $formType|@count > 0}
<form method='post' action='{routeShort action="option"}'>
{foreach from=$formType name=foo item=item}
	<div class="zone_titre">
		<h2>{t}Form{/t} {$item['name']}</h2>
	</div>

	<div>
	{$item["form"]->{$item["form_field"]["activation"]}}
	{$item["form"]->{$item["form_field"]["save"]}}
	</div>
	
	{if $smarty.foreach.foo.last}
		<div class="form_line" id="form_save">
			<div class="form_elem"><button class="btn btn-success" name="save" value="Save"><div>{t}Save{/t}</div></button></div>
		</div>
	{/if}

{/foreach}
</form>
{/if}

</div>

{literal}
<script type="text/javascript">
	
	$(document).ready(function(){
		$("div[id*='activation']").change(function(){
			if ($(this).find("input[id*='activation']").is(':checked') == false)
				$(this).next('div').find("input[id*='save']").attr("disabled",true);
			else
				$(this).next('div').find("input[id*='save']").attr("disabled",false);
		});
	});
	
</script>
{/literal}
