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

{include file="{$smarty.const.APPLICATION_PATH}/modules/packager/views/back/menu.tpl" active="blocs"}

<div id="content">
	<table id="datatable" class="table table-bordered table-striped table-hover dataTable">
		<thead>
				<tr>
					<th>{t}Name{/t}</th>
					<th>{t}Version{/t}</th>
					<th>{t}Type{/t}</th>
					<th>{t}Description{/t}</th>
					<th>{t}Action{/t}</th>
				</tr>
			</thead>
			
			{if $blocs != null}
				<tbody>
					{foreach from=$blocs item=item}
						<tr>
							<td>{$item.name}</td>
							<td>{$item.version}</td>
							<td>{$item.type}</td>
							<td>{if isset($item.description)}{$item.description}{/if}</td>
							<td>
								<a href="#" data-name="{$item.name}" class="changeStatBloc btn btn-mini showTooltip {if $item.load}btn-danger{else}btn-success{/if}" title="{if $item.load}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
									<i class="icon-off icon-white"></i>
								</a>
							</td>
						</tr>
					 {/foreach}
				 </tbody>
			{/if}
	</table>
</div>

<script>
{literal}
$(document).ready(function(){
	$('.changeStatBloc').on('click', function(){
		$.ajax({
			url : baseUrl+'/api/packager/package/change-state-bloc',
			data : {
				name : $(this).data('name')
			},
			cache: false,
			type: 'POST',
			success : function(e){
				if (!e.error) {
					$('.changeStatBloc[data-name="' + e.blocName + '"]').parent().effect("highlight", {}, 1000);		
					if (e.actualStat == 'disable'){
						$('.changeStatBloc[data-name="' + e.blocName + '"]').removeClass('btn-danger').addClass('btn-success').attr('title', {/literal}'{t}Enable{/t}'{literal});
					}else if (e.actualStat == 'enable')
						$('.changeStatBloc[data-name="' + e.blocName + '"]').removeClass('btn-success').addClass('btn-danger').attr('title', {/literal}'{t}Disable{/t}'{literal});
				} 
			}
		});
		return false;
	});
});
{/literal}
</script>