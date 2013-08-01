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
	<h1>{t}Contact{/t}</h1>
	<div>{t}Configuration des destinataires pour les formulaires{/t}</div>
</div>

{if $backAcl->hasPermission("mod_contact", "view")}
<a title="{t}Reload{/t}" href="{routeShort action="reload"}" class="btn btn-success">
	<i class="icon-refresh icon-white"></i>{t}Reload forms{/t}
</a>
{/if}

{if $backAcl->hasPermission("mod_contact", "editOption")}
	<a title="{t}Option{/t}" href="{routeShort action="option"}" class="btn btn-warning fancybox">
		<i class="icon-pencil icon-white"></i>{t}Options{/t}
	</a>
{/if}
			
{if $formType|@count > 0}
	
	<form class="form-horizontal" method='post' action='{routeShort action="index"}'>
		{foreach from=$formType name=foo item=item}
			
			<div class="zone">
				<div class="zone_titre">
					<h2>
						{t}Form{/t} : {$item["name"]} {if $item["error_mail"]}<span style="color: #f80">[{t}No Email saved{/t}]</span>{/if}
						<div style="float:right;">
						{t}Number of submission saved{/t} : {$item["nb_submission"]}
						{if $backAcl->hasPermission("mod_contact", "export")}
							<a title="{t}Option{/t}" href="{routeShort action="export" id=$item['name']}" class="btn btn-success">
								<i class="icon-pencil icon-white"></i>{t}Export{/t}
							</a>
						{/if}
						</div>
					</h2>
				</div>
				
				<div class="showInformations" style="cursor:pointer;font-size:1em;padding-bottom:4px;color:#93BBEB;">{t}See informations{/t}</div>
				<div class="hideInformations" style="cursor:pointer;font-size:1em;padding-bottom:4px;color:#93BBEB;">{t}Hide informations{/t}</div>
				
				<div class="section_content">
					
					{$item["form"]->{$item["form_field"]["typeContact"]}}
					
					<div class="type_classique" {if $item["values"]["typeSelect"] == 1}style="display:none;"{/if}>
						{$item["form"]->{$item["form_field"]["emails"]}}
						{$item["form"]->{$item["form_field"]["emailsCci"]}}
					</div>
					
					<div class="type_select" {if $item["values"]["typeSelect"] == 0}style="display:none;"{/if}>
						<div class="default" style="display:none;">
							<div class="entry">
								
								{$item["form"]->{$item["form_field"]["selectName"]}}
								{$item["form"]->{$item["form_field"]["selectMails"]}}
								
							</div>
						</div>
						
						{foreach from=$item.datasSelect name=select item=data}
							<div class="entry">
								<div id="form_{$item['name']}selectName" class="form_line">
									<div class="form_text">
										<div class="form_label">
											<label for="{$item['name']}selectName">{t}Name{/t}</label>
										</div>
										<div class="form_desc">{t}Name in select{/t}</div>
									</div>
									
									<div class="form_elem">
										<input type="text" helper="formText" value="{$data['name']}" id="{$item['name']}selectName" name="{$item['name']}selectName-{$smarty.foreach.select.index+1}">
									</div>
									
								</div>
								
								<div id="form_{$item['name']}selectMails" class="form_line">
									<div class="form_text">
										<div class="form_label">
											<label for="{$item['name']}selectMails">{t}Recipient email addresses{/t}</label>
										</div>
										<div class="form_desc">{t}Separated by semicolons{/t}</div>
									</div>
									
									<div class="form_elem">
										<input type="text" helper="formText" value="{$data['emails']}" id="{$item['name']}selectMails" name="{$item['name']}selectMails-{$smarty.foreach.select.index+1}">
									</div>
									
								</div>
							</div>
						{/foreach}
						
						<input type="button" value="{t}Add entry{/t}" name="addSelect" id="addSelect" style="margin-left: 205px;margin-bottom : 10px;" />
					</div>
					
					{$item["form"]->{$item["form_field"]["content"]}}
					
					{if $item["has_email_reply"]}
						{$item["form"]->{$item["form_field"]["response_check"]}}
						{$item["form"]->{$item["form_field"]["auto_response"]}}
					{/if}
										
				</div>
				<div class="clearfix"></div>
			</div>
			
			{if $smarty.foreach.foo.last}
				<div class="form_line" id="form_save">
					<div class="form_elem"><button class="btn btn-success" name="save" value="Save"><div>{t}Save{/t}</div></button></div>
				</div>
			{/if}
		{/foreach}
		
	</form>
	
{else}
	<div class="zone">
		<div class="section_content">
			{t}No form contact{/t}
		</div>
	</div>
{/if}

{if $backAcl->hasPermission("mod_contact", "manage")}
	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>{t}Choose your options{/t}</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
		</div>
		<div class="droits_content">
			
			<form action="{$formAcl->getAction()}" method="post"> 
				<div class="aclBloc">
					{$formAcl}
				</div>
				<div class="droits_submit">
					{$formAcl->submit}
				</div>
			</form> 
			
		</div>
	</div>
{/if}


{literal}
<script type="text/javascript">
	
	$(document).ready(function(){
		
		$(".fancybox").fancybox({
			'scrolling'		: 'auto',
			'width'			: '75%',
			'height'		: '100%',
			'titleShow'		: false,
			'autoScale'		: true,
			'type'		: 'iframe',
			'onClosed' : function(){$(location).attr('href',"");}
		});
		
		$("div[id*='auto_response']").each(function(){
			if ($(this).prev().find("input[id*='response_check']").is(':checked') == false)
			{
				$(this).hide();
			}
		});
		
		$(".section_content").each(function(){
			$(this).hide();
			$(this).parent().find(".hideInformations").hide();
			$(this).parent().find(".showInformations").show();
		});
		
		$(".showInformations, .hideInformations").on("click", function(){
			$(this).parent().find(".section_content").toggle();
			$(this).parent().find(".hideInformations").toggle();
			$(this).parent().find(".showInformations").toggle();
		});
		
		// Affichage des bonnes informations lors de l'ouverture de la page
		$("input:radio:checked").each(function(){
				if($(this).parent().text() == 'Select')
				{
					$(this).parent().parent().parent().parent().find(".type_select").show();
					$(this).parent().parent().parent().parent().find(".type_classique").hide();
				} else {
					$(this).parent().parent().parent().parent().find(".type_select").hide();
					$(this).parent().parent().parent().parent().find(".type_classique").show();
				}
		});
		
		// A chaque changement de choix sur le select ou multi select
		$("input:radio").on("change", function() {
			
			if( $(this).val() == 0 ) {
				$(this).parent().parent().parent().parent().find(".type_select").hide();
				$(this).parent().parent().parent().parent().find(".type_classique").show();
			}
			else if ( $(this).val() == 1 ) {
				$(this).parent().parent().parent().parent().find(".type_classique").hide();
				$(this).parent().parent().parent().parent().find(".type_select").show();
			}
			
		});
		
		// Affichage des réponses auto quand on coche la case
		$("input[id*='response_check']").on("change", function() {
			if( $(this).is(':checked'))
				$(this).parent().parent().next("div[id*='auto_response']").show();
			else 
				$(this).parent().parent().next("div[id*='auto_response']").hide();			
		});
		
		$(".type_select #addSelect").on("click", function(e){
			e.preventDefault();
			
			var selector = $(this).parent();
			
			var input = $(selector).find(".default .entry").clone();
			var count = $(selector).find(".entry").size();
			
			$(input).find("input").each(function(i){
				$(this).attr("name", $(this).attr("name") + "-" + count);
			});
			
			$(this).before(input);
		});
		
	});
	
</script>
{/literal}
