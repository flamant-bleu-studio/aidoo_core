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

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Edit a Menu Item{/t}</h2>
		<div>{t}Edit item in this menu{/t}</div>
	</div>
	
	<form action='#' method='post' id="{$form->getId()}">
		{$form->label}
		{$form->subtitle}
		
		{$form->access}
		
		{$form->hidetitle}
		{$form->loadAjax}
		{$form->image}
		
		{$form->cssClass}
		{$form->tblank}
		
		{$form->linkType}
		{$form->chooseType}
		
		<div class="form_line newgroup">
			<div class="form_elem">
			{foreach from=$form->newgroup key=key item=item}
				<div class="pull-left"><button id="{$key}" class="typeChoice"></button></div>
			{/foreach}
			</div>
			<div class="clear"></div>
		</div>
		
		{$form->existinggroup}
		{$form->externalgroup}
		<div class="droits_submit">
			{$form->submit}
		</div>
	</form>
	
</div>

<script type="text/javascript">

	if ($('#linkType-1').is(':checked'))
	{
		$('.newgroup').show();
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	}

	if ($('#linkType-2').is(':checked'))
	{
		$('.newgroup').hide();
		$('.existinggroup').show();
		$('.externalgroup').hide();
	}
	
	if ($('#linkType-3').is(':checked'))
	{
		$('.newgroup').hide();
		$('.existinggroup').hide();
		$('.externalgroup').show();
	}
	
	
	$('#linkType-1').bind('click', function(){
		$('.newgroup').show();
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	});
	
	$('#linkType-2').bind('click', function(){
		$('.newgroup').hide();
		$('.existinggroup').show();
		$('.externalgroup').hide();
	});
	
	$('#linkType-3').bind('click', function(){
		$('.newgroup').hide();
		$('.existinggroup').hide();
		$('.externalgroup').show();
	});
	
	$(".typeChoice").bind('click',function(){
		$("#chooseType").val($(this).attr('id'));
		$(".typeChoice").removeClass('active');
		$(this).addClass('active');
		return false;
	});    

</script>
