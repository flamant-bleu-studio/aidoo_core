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

{$formAcl->baliseFormOpen}

<input type="hidden" name="ACL[permission_name]" value="{$formAcl->permission_name}" />

	<table id="backAclManagement">
		<tr>
			<th></th>
			{foreach from=$formAcl->modes key=name item=m}
			<th>{$name}</th>
			{/foreach}
		</tr>
		
		{foreach from=$formAcl->groups item=g}
		<tr>
			<td class="group_name">&nbsp;{$g->level}{$g->name}&nbsp;</td>
			{foreach from=$formAcl->modes key=name item=m}
			{assign var='test' value="_"|cat:$g->id}
			<td><input type="checkbox" name="ACL[{$name}-{$g->id}]" id="{$name}-{$g->id}" class="aclCheckbox {$name}-{$g->parent}" {if isset($m[$test]) && $m[$test] == 1} checked{/if} /></td>
			{/foreach}
		</tr>
		{/foreach}
	
	</table>
{$formAcl->baliseFormClose}

<script language="javascript">
{literal}
$(document).ready(function(){
	$(function() {
	   $('#backAclManagement .aclCheckbox').change( function() {
		   	var cases = $('.'+$(this).attr('id'));
		   	if(this.checked == true)
		   		cases.attr('checked', this.checked).attr('disabled', this.checked).trigger('change');
		   	else
		   		cases.attr('disabled', this.checked).trigger('change');		   	
	   });
	});
	$('input:checked').trigger('change');
});
{/literal}
</script>
