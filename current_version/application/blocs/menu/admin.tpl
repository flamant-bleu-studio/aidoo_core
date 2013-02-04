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

{$form->align}
{$form->desactiveDeroulant}
{$form->idMenu}

{$form->test}

{$form->levelDisplay}
{$form->displayOnlyFolder}
{$form->separator}


{literal}
<script type="text/javascript">
$(document).ready(function(){
	
	/** Get active folder (EDIT) **/
	var activeFolder = {/literal}{if $idFolder}{$idFolder}{else}0{/if}{literal};
	var activeMenu = {/literal}{if $idMenu}{$idMenu}{else}0{/if}{literal};

	/** Init **/
	updateSelectFolder($("#idMenu").val());

	if( activeFolder && activeMenu)
	{
		$("#fieldset-test #form_folder_menu_" + activeMenu + " select").val(activeFolder);
	}
	
	/** If menu select change **/
	$("#idMenu").change(function(){
		updateSelectFolder($("#idMenu").val());
	});
	
	/** Update list folders **/
	function updateSelectFolder(menuId) {
		
		$("#fieldset-test dl").children().each(function(){
			$(this).hide();
		});
		
		$("#fieldset-test #form_folder_menu_" + menuId).show();
	}
});
</script>
{/literal}
