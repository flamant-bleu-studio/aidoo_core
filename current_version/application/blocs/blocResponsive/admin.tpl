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

{$form->background_color}
{$form->background_image}
{$form->text}
{$form->text_color}
{$form->background_text}
{$form->icon}

<div style="display:none;width: 80px;height: 80px;margin-left: 200px;margin-bottom: 15px;background-color: #A9A9A9;padding: 5px;">
	<img id="preview_icon" src="" data-origin="http://{$smarty.server.SERVER_NAME}{$baseUrl}/skins/{$smarty.const.SKIN_FRONT}/icon/" />
</div>

{$form->link_type}
{$form->link_internal}
{$form->link_external}
{$form->link_target_blank}
{$form->load_ajax}

<script type="text/javascript">
$(document).ready(function(){
	if ($('#form_icon select').val() != 0) {
		$('#preview_icon').parent().show();
		$('#preview_icon').attr('src', $('#preview_icon').data('origin')+$('#form_icon select').val()+'.png');
	}
	
	$('#form_icon').on('change', 'select', function(){
		$('#preview_icon').parent().show();
		$('#preview_icon').attr('src', $('#preview_icon').data('origin')+$(this).val()+'.png');
		
		if ($(this).val() == 0)
			$('#preview_icon').parent().hide();
		
	});
});
</script>