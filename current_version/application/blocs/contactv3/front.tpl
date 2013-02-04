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

<div class="form-{$type_form} {$form->getId()}">
	
	<span class="error" style="display:none;"></span>
	
	<div class='contact_content'>{$content}</div>
	<div class="contact_form">{$form}</div>
	
</div>

{literal}
<script type="text/javascript">

$(document).ready(function(){
	$("#{/literal}{$form->getId()}{literal}").submit(function(e){
		e.preventDefault();

		if($(this).validationEngine('validate')){

			$.ajax({
				type: "POST",
				url: baseUrl+'/ajax/contact/sendnew',
				dataType: "json",
				data: {
					'values':$(this).serializeArray(),
					'type_form':'{/literal}{$type_form}{literal}',
					'url': '{/literal}{$smarty.server.REQUEST_URI}{literal}'
				},
				error: function(results){
					alert("Une erreur est survenue ...\nActualisez la page et réessayez.");
				},
				success: function(results){
					if(results["error"] == true)
					{
						if(results["message"])
						{
							
							$(".{/literal}{$form->getId()}{literal} form").slideUp();
							$(".{/literal}{$form->getId()}{literal} span.error").empty();
							$(".{/literal}{$form->getId()}{literal} span.error").append(results["message"]);
							$(".{/literal}{$form->getId()}{literal} span.error").css("display", "inline");
						}
						else
							alert("Une erreur est survenue ...\nActualisez la page et réessayez.");
					}
					else
					{
						$("#{/literal}{$form->getId()}{literal}").find('input').not('[type=submit]').val('');
						alert('Message envoyé avec succès.');
					}
				}
			});
		}
		return false;
	});	
});

	
</script>
{/literal}
