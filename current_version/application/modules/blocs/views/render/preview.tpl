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

{function name=generatePreviewTemplate}
	<div class="preview_template" id="template_{if isset($id) && $id == '0'}0{else}{$template->id_template}{/if}">
		<table width="50%" cellspacing="0" cellpadding="0">
			<tr>
				<th>{t}Left column{/t}</th>
				<th>{t}Right column{/t}</th>
			</tr>
			
			<tr>
				<td>
					{if $sidebar[$template->id_template]["sideleft1"]}
						<ul>
							{foreach name=blocs from=$sidebar[$template->id_template]["sideleft1"] item=item}
								<li>{$blocs[$item]->designation}</li>
							{/foreach}
						</ul>
					{else}
						Aucun bloc
					{/if}
				</td>
				<td>
					{if $sidebar[$template->id_template]["sideright1"]}
						<ul>
							{foreach name=blocs from=$sidebar[$template->id_template]["sideright1"] item=item}
								<li>{$blocs[$item]->designation}</li>
							{/foreach}
						</ul>
					{else}
						Aucun bloc
					{/if}
				</td>
			</tr>
		</table>
	</div>
	
	<div class="clear"></div>
{/function}


<!-- Gérer les droits d'accès à cette page ! -->

<div id="parent_select_diaporama" class="form_line">
	<div class="form_text">
		<div class="form_label">
			<label for="select_diaporama">{t}Choose diaporama{/t}</label>
		</div>
		<div class="form_desc">{t}Select diaporama in this list{/t}</div>
	</div>
	
	<div class="form_elem">
		<select id="select_diaporama" name="select_diaporama_page">
			{html_options options=$listDiaporamas selected={$activeDiaporamaId}}
		</select>
	</div>
	
	<div class="clear"></div>
</div>

<div id="preview_template" class="form_line">
	<div class="form_text">
		<div class="form_label">
			<label for="select_template">{t}Choose template{/t}</label>
		</div>
		<div class="form_desc">{t}Select template in this list{/t}</div>
	</div>
	
	<div class="form_elem">
		<select id="select_template" name="select_template_page">
			{html_options options=$templatesToSelect selected={$activeTemplateId}}
		</select>
	</div>
	
	<div class="clear"></div>
	
	{call name=generatePreviewTemplate template=$defaultTemplate[0] id='0'}
	
	{foreach name=templates from=$templates item=template}
		{call name=generatePreviewTemplate template=$template}
	{/foreach}
	
</div>

{literal}
<script type="text/javascript">
$(document).ready(function(){
	
	hideTemplates(); // Hide ALL
	showTemplate($("#select_template option:selected").val()); // Show selected select template
	
	/** Hide all div preview template **/
	function hideTemplates() {
		$(".preview_template").each(function(){
			$(this).hide();
		});
	}
	
	/** Show div preview template with id **/
	function showTemplate(id) {
		$("#template_"+id).show();
	}
	
	/** Changement de template **/
	$("#select_template").change(function(){
		hideTemplates();
		showTemplate($(this).val());
	});
	
});
</script>
{/literal}
