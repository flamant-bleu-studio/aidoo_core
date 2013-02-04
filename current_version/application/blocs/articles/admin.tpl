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

{$form->imageFormat}

{$form->showArchive}
<div id="show_textarchive" {if !$form->showArchive->getValue()}style="display:none;"{/if} >
	{$form->textArchive}
</div>

{$form->showDate}
<div id="show_dateformat" {if !$form->showDate->getValue()}style="display:none;"{/if} >
	{$form->dateFormat}
</div>

{$form->truncateText}
{$form->alignment}
{$form->displayMode}
	
{$form->fromMode}

<div id="show_from_cat" {if $form->fromMode->getValue() != "category" && $form->fromMode->getValue() != ""}style="display:none;"{/if}>
	{$form->category}
	
	{$form->nb_article}
	
	<div id="show_multi-slides" {if $form->displayMode->getValue() == "0"}style="display:none;"{/if} >
		{$form->nb_page}
		{$form->scrolling}
		
		<div class="show_mode-slide" {if $form->displayMode->getValue() != "1"}style="display:none;"{/if}>
			{$form->autoStart}
		</div>
		
		{$form->stopHover}
		
		<div class="show_mode-slide" {if $form->displayMode->getValue() != "1"}style="display:none;"{/if}>
			{$form->showPagination}
			
			<div id="show_mode-slide-pagination" {if $form->showPagination->getValue() != "1"}style="display:none;"{/if}>
				{$form->pagerPosition}
			</div>
			
			{$form->showArrow}
		</div>
		
		<div id="show_mode-ticker" {if $form->displayMode->getValue() != "2"}style="display:none;"{/if}>
			{$form->tickerSpeed}
		</div>
		
	</div>
</div>
<div id="show_from_select" {if $form->fromMode->getValue() == "category" || $form->fromMode->getValue() == ""}style="display:none;"{/if}>
	<div class="datatable">
		<table id="datatable" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th style="width:120px;text-align:center;" class="no_sorting">Sélectionner ?</th>
					<th>{t}Title{/t}</th>
					
				</tr>
			</thead>
			<tbody>
			
			{if $articlesLst != null}
			{foreach from=$articlesLst item=a}
				<tr>
					<td style="text-align:right;"><input type="checkbox" name="selection[{$a->id_article}]" {if in_array($a->id_article, $selection)}checked{/if}/></td>
					<td>{$a->title}</td>
				</tr>
			{/foreach}
			{/if}
			
			</tbody>
		</table>
		<div class="clear"></div>
	</div>
</div>

{literal}
<script type="text/javascript">

	$('select[name=fromMode]').on("change", function(){
		if($(this).val() == "selection"){
			$("#show_from_cat").hide();
			$("#show_from_select").show();
		}
		else if($(this).val() == "category"){
			$("#show_from_cat").show();
			$("#show_from_select").hide();
		}
		else {
			alert("Sélection invalide");
		}
	});
	$('input[name=showArchive]').on("click", function(){
		$("#show_textarchive").toggle($(this).is(":checked"));
	});
	
	$('input[name=showDate]').on("click", function(){
		$("#show_dateformat").toggle($(this).is(":checked"));
	});
	
	$('select[name=displayMode]').on("change", function(){

		var val = $(this).val();

		if(val == 1){
			$(".show_mode-slide").show()
		}
		else {
			$(".show_mode-slide").hide()
		}
		if(val == 2){
			$("#show_mode-ticker").show()
		}
		else {
			$("#show_mode-ticker").hide()
		}
		
		if(val == 1 || val == 2){
			$("#show_multi-slides").slideDown()
		}
		else {
			$("#show_multi-slides").slideUp()
		}
	});
	
	$('input[name=showPagination]').on("click", function(){
		$("#show_mode-slide-pagination").toggle($(this).is(":checked"));
	});
	
</script>
{/literal}
