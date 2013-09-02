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
{$form->text}
{$form->text_color}
{$form->icon}

<div style="width: 80px;height: 80px;margin-left: 200px;">
	<img id="preview_icon" src="" data-origin="http://{$smarty.server.SERVER_NAME}{$baseUrl}/skins/{$smarty.const.SKIN_FRONT}/icon/" />
</div>

{$form->id_page}

<script type="text/javascript">
$(document).ready(function(){
	$('#form_icon').on('change', 'select', function(){
		$('#preview_icon').attr('src', $('#preview_icon').data('origin')+$(this).val()+'.png');
	});
});
</script>