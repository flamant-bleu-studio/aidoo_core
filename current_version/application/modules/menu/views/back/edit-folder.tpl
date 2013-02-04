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
		<h2>{t}Edit folder{/t}</h2>
		<div>{t}Modification of a folder{/t}</div>
	</div>
	
	<form action="{$form->getAction()}" method="post" id="{$form->getId()}">
	
		{$form->id}
		{$form->menu_id}
		
		{$form->label}
		
		{$form->hidetitle}
		{$form->image}
		
		{$form->linkType}
		
		<div class="form_line newgroup">
			<div class="form_elem">
			{foreach from=$form->newgroup key=key item=item}
				<div class="left"><button id="{$key}" class="typeChoice"></button></div>
			{/foreach}
			</div>
			<div class="clear"></div>
		</div>
		
		{$form->existinggroup}
		{$form->externalgroup}
		
		{$form->access}
		{$form->permissions}
		
		<div class="droits_submit">
			{$form->submit}
		</div>
	</form>
	
</div>		

<script type="text/javascript">

	if ($('#linkType-1').is(':checked'))
	{
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	}

	if ($('#linkType-2').is(':checked'))
	{
		$('.existinggroup').show();
		$('.externalgroup').hide();
	}
	
	if ($('#linkType-3').is(':checked'))
	{
		$('.existinggroup').hide();
		$('.externalgroup').show();
	}
	
	if ($('#linkType-4').is(':checked'))
	{
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	}
	
	
	$('#linkType-1').bind('click', function(){
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	});
	
	$('#linkType-2').bind('click', function(){
		$('.existinggroup').show();
		$('.externalgroup').hide();
	});
	
	$('#linkType-3').bind('click', function(){
		$('.existinggroup').hide();
		$('.externalgroup').show();
	});
	
	$('#linkType-4').bind('click', function(){
		$('.existinggroup').hide();
		$('.externalgroup').hide();
	});
	
	
	$(".typeChoice").bind('click',function(){
		$("#chooseType").val($(this).attr('id'));
		$(".typeChoice").removeClass('active');
		$(this).addClass('active');
		return false;
	});    

</script>
