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

<div class="row-fluid">
	
	<div class="span3 well">
		<div class="zone_titre">
			<h2 style="float:left;">Calendriers</h2>
			<div class="clearfix"></div>
		</div>
		
		<ul class="unstyled">
			{foreach from=$calendars item=c}
				<li>
					{if $c->color}<div style="display:inline-block;background-color:#{$c->color};width: 10px;height:10px;"></div>{/if}
					
					{$c->name}
					
					<div class="actions" style="float: right;">
						<a href="{routeShort action="edit-calendar" id={$c->id_calendar}}" class="fancybox btn btn-warning btn-mini show_tooltip" title="{t}Edit{/t}">
							<i class="icon-pencil icon-white"></i>
						</a>
						<a href="{routeShort action="delete-calendar" id={$c->id_calendar}}" class="btn btn-danger btn-mini show_tooltip" title="{t}Delete{/t}" onClick="confirmDelete(this.href, '<h1>{t}Delete this calendar ?{/t}</h1> {t}All associated events will be also deleted{/t}', '{t}Delete{/t}', '{t}Cancel{/t}');return false;">
							<i class="icon-trash icon-white"></i>
						</a>
					</div>
				</li>
				
				<br />
				
			{/foreach}
		</ul>
		
		<a href="{routeShort action="add-calendar"}" class="show_tooltip fancybox btn btn-success" title="Créez un calendrier"><i class="icon-plus icon-white"></i> Ajout un calendrier</a>
		<br /><br />
		
		<a href="{routeShort action="add-event"}" class="show_tooltip fancybox btn btn-success{if !$calendars} disabled{/if}" title="{if !$calendars}Vous devez d'abord créer un calendrier{else}Créez un évennement{/if}"><i class="icon-plus icon-white"></i> Ajout un évenement</a>
	</div>
	
	<div id="calendar_content" class="span9">
		{$calendar}
	</div>
</div>

<script type="text/javascript">

function calendarInitAjax() {
	$(".fancybox:not(.disabled)").fancybox({
		'scrolling'		: 'auto',
		'width'			: '75%',
		'height'		: '100%',
		'titleShow'		: false,
		'autoScale'		: true,
		'type'		: 'iframe'
	});
}

$("a.disabled").on('click', function(){
	return false;
});

$(document).ready(function() {

	$('#calendar_content #navigate a').live('click', function(e){
		e.preventDefault();
		
		var current_date = $('table#calendar:visible').data('current');
		var action = $(this).data('action');
		
		$.ajax({
			url: '/administration/calendar/render',
			data: {
				'format': 'html',
				'current': current_date,
				'action': action
			},
			type: 'POST',
			success: function(result){
				$('#calendar_content').empty();
				$('#calendar_content').append(result);
			}
		});
	});
});
</script>
