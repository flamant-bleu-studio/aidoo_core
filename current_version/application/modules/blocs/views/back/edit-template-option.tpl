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
	<h1>{t}Module article{/t}</h1>
	<div>{t}Manage options{/t}</div>
</div>

<div class="zone">
	<form action="{$form->getAction()}" method="post" id="{$form->getId()}"> 
		
		<div class="zone_titre">
			<h2>{t}General{/t}</h2>
		</div>
		
		{$form->title}
		{$form->theme}
		{$form->classCss}
		
		<div class="zone_titre">
			<h2>{t}Options{/t}</h2>
		</div>
		
		{$form->defaut}
		
		<div class="zone_titre">
			<h2>{t}Background{/t}</h2>
		</div>
		
		{$form->bgType}
		
		{$form->bgPicture}
		{$form->bgRepeat}
		
		{$form->bgColor1}
		{$form->bgColor2}
		
		{$form->bgGradient}
		
		<div class="row-fluid form_submit">
			<button class="btn btn-large btn-primary" name="submit" value="true">{t}Save{/t}</button>
		</div>
		
	</form>
</div>


<script type="text/javascript">
$(document).ready(function(){
	
	displayFormBackground($('#bgType').val());
	
	$('#bgType').on('change', function(){
		displayFormBackground($(this).val());
	});
	
	function displayFormBackground(val) {
		
		$('#form_bgPicture, #form_bgRepeat, #form_bgColor1, #form_bgColor2, #form_bgGradient').hide();
		
		/** Picture **/
		if (val == 1) {
			$('#form_bgPicture, #form_bgRepeat, #form_bgColor1').show();
		}
		/* Solid color */
		else if(val == 2) {
			$('#form_bgColor1').show();
		}
		/* Gradient color */
		else if(val == 3) {
			$('#form_bgColor1, #form_bgColor2, #form_bgGradient').show();
		}
	}
	
});
</script>
