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

{$form->mode}

<div id="specific" {if $form->mode->getValue() != "specific"}style="display:none;"{/if}>
	{$form->diaporamaId}
</div>

<div id="byPage" {if $form->mode->getValue() != "byPage"}style="display:none;"{/if}>
	{$form->diaporamaIdPage}
</div>

{$form->bx_type}

<div id="noTicker" {if $form->bx_type->getValue() == "ticker"}style="display:none;"{/if}>
	{$form->pause}
	{$form->displaySlideQty}
	{$form->moveSlideQty}
	{$form->pagination}
</div>


<div class="clearfix"></div>
{literal}
<script type="text/javascript">
	$(document).ready(function(){
		
		$('#bx_type').on('change', function(){
			var isTicker = ($(this).val() == "ticker");
			
			$('#noTicker').toggle(!isTicker);
			
		});
		
		$('#mode').on('change', function(){
			
			if($(this).val() == "specific"){
				$("#specific").show();
				$("#byPage").hide();
			}
			else {
				$("#specific").hide();
				$("#byPage").show();
			}
		});

	});
</script>
{/literal}
