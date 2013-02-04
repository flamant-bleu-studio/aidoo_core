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

{appendFile type="js" src="{$smarty.const.COMMON_LIB_PATH}/lib/langSwitcher/script.js"}
{appendFile type="css" src="{$smarty.const.COMMON_LIB_PATH}/lib/langSwitcher/styles.css"}

<script type="text/javascript" src="{$baseUrl}/lib/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

tinyMCE.init({
	mode : "specific_textareas",
    editor_selector : "mceEditorMiddle",
    theme : "advanced",
    plugins : "emotions,spellchecker,advhr,insertdatetime,preview", 
            
    // Theme options - button# indicated the row# only
    theme_advanced_buttons1 : "bold,italic,underline,|,bullist,numlist",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true
});

</script>

<div class="formulaire">
	<form id="{$form->getId()}" enctype="multipart/form-data" action="{$form->getAction()}" method="post">
		{$form->category}
		
		{$form->id}
		
		{$form->title}
		{$form->image}
		{$form->chapeau}
		
		{$form->readmore_elements}
		
		{$form->submit}
	</form>
</div>
