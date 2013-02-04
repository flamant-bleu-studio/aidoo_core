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

{$form->typeDiapo}

<div id="params">
	<div id="params_diaporama">
		{$form->diaporamaId}
	</div>
	
	<div id="params_diaporamaPage">
		{$form->diaporamaIdPage}
	</div>
</div>

{$form->paginationActive}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		
		hideParams();
		showParam($('input[type=radio]:checked').val());
		
		function hideParams() {
			$('#params>div').each(function(){
				$(this).hide();
			});
		}
		
		function showParam(name) {
			$("#params_"+name).show();
		}
		
		$('input[type=radio]').change(function(){
			hideParams();
			showParam($('input[type=radio]:checked').val());
		});
	});
</script>
{/literal}
