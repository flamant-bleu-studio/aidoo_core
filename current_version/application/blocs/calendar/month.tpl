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

<div class="content">
{$calendar}
</div>

<script type="text/javascript">

$(document).ready(function() {
	
	$('.bloc-{$id} #navigate a').live('click', function(e){
		e.preventDefault();
		
		var current_date = $('table#calendar:visible').data('current');
		var action = $(this).data('action');
		
		$.ajax({
			url: '/calendar/render/{$id}',
			data: {
				'format': 'html',
				'current': current_date,
				'action': action
			},
			type: 'POST',
			success: function(result){
				$('.bloc-{$id} .content').empty();
				$('.bloc-{$id} .content').append(result);
			}
		});
	});
});

</script>
