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

<div id="navigate" style="text-align:center;margin-bottom: 15px;">
	<a class="btn btn-mini prev" href="#" data-action="prev"><</a>
	<div style="" class="current_month">{$current_month}</div>
	<a class="btn btn-mini next" href="#" data-action="next">></a>
</div>

{assign var="days_in_this_week" value=1}
{assign var="day_counter" value=0}

<table id="calendar" class="table table-bordered" data-current="{$datetime->format('Y-m-d')}">
	
	<thead>
		<tr>
			<th>Lun.</th>
			<th>Mar.</th>
			<th>Mer.</th>
			<th>Jeu.</th>
			<th>Ven.</th>
			<th>Sam.</th>
			<th>Dim.</th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			
			<!-- Avant le mois courant -->
			
			{section name=first_week start=0 loop=($start_day-1) step=1}
				<td class="disable"><div class="case"></div></td>
				{assign var="days_in_this_week" value=$days_in_this_week+1}
			{/section}
			
			<!-- Le mois courant -->
			
			{section name=list_days start=1 loop=($days_in_month+1) step=1}
				
				{if $days_in_this_week == 8}
					</tr>
					<tr>
					{assign var="days_in_this_week" value=1}
				{/if}
				
				{assign var="date_formated" value=$datetime->format('Y-m-d')}
				
				
				<td>			
					<div class="day{if $map_events[$date_formated]}{foreach from=$map_events[$date_formated] item=id_event} calendar-{$events[$id_event]->id_calendar} event-{$id_event}{/foreach}{/if}{if $datetime == $current_date} current{/if}">
						{$smarty.section.list_days.index}
					</div>
				</td>
				
				{assign var="days_in_this_week" value=$days_in_this_week+1}
				{assign var="datetime" value=$datetime->modify('next day')}
				
			{/section}
			
			<!-- Après le mois courant -->
			
			{section name=first_week start=0 loop=(8-$days_in_this_week) step=1}
				<td class="disable"><div class="case"></div></td>
				{assign var="days_in_this_week" value=$days_in_this_week+1}
			{/section}
			
		</tr>
	</tbody>
	
</table>
