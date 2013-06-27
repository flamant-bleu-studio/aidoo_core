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
	<h1>{t}Packages{/t}</h1>
	<div>{t}Manage packages{/t}</div>
</div>

{if isset($existingFiles) && $existingFiles != null}
	<h3>{t}Warning, installing this package will overwrite the following files:{/t}</h3>
	
	{foreach from=$existingFiles item=item}
		<h2>{$item} {t}already exists{/t}</h2>
	{/foreach}
	
	<div class="left">
		<a href="{$adminBaseUrl}/packager/forceinstall/{$uploadedPackage}" class="btn btn_vert"><div>{t}Continue and overwrite existing files{/t}</div></a>
	</div>
	
	<div class="left">
		<a href="{$adminBaseUrl}/packager/" class="btn btn_rouge"><div>{t}Cancel{/t}</div></a>
	</div>
	
	<div class="clear"></div>
{/if}

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Module List{/t} <span class="helper"></span></h2>
		<div>{t}Module List{/t}</div>
	</div>

	<table class="datatable table table-bordered table-striped show_tooltip">
		<thead>
			<tr>
				<th>{t}Name{/t}</th>
				<th>{t}Version{/t}</th>
				<th>{t}Type{/t}</th>
				<th>{t}Description{/t}</th>
				<th>{t}Action{/t}</th>
			</tr>
		</thead>
		
		{if $moduleList != null}
			<tbody>
				{foreach from=$moduleList item=item}
				 	{cycle values="second,first" assign="placeholdercolor"}
					<tr class='{$placeholdercolor}'>
						<td>{$item.name}</td>
						<td>{$item.version}</td>
						<td>{$item.locationPath} {$item.type}</td>
						<td>{if isset($item.description)}{$item.description}{/if}</td>
						<td>
							{if (isset($item.deactivable)) && ($item.deactivable == "true")}
								<a href="{routeShort action="load-unload-module" id=$item.name type=$item.type}" data-name="{$item.name}" class="changeStatModule btn btn-mini {if $item.load}btn-danger{else}btn-success{/if}" data-original-title="{if $item.load}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
									<i class="icon-off icon-white"></i>
								</a>
							{/if}
							
							{if (isset($item.uninstallable)) && ($item.uninstallable == "true")}
								<a href="{routeShort action="uninstall" id=$item.name type=$item.type}" class="btn btn-mini btn-danger" data-original-title="{t}Uninstall{/t}"
								onClick="return confirm('{t}Are you sure to delete this module ?{/t}')">
									<i class="icon-trash icon-white"></i>
								</a>
							{/if}
						</td>
					</tr>
				 {/foreach}
			 </tbody>
		{/if}
		
	</table>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Bloc List{/t} <span class="helper"></span></h2>
		<div>{t}Bloc List{/t}</div>
	</div>
	
	<div>
		<table class="datatable table table-bordered table-striped show_tooltip">
			<thead>
				<tr>
					<th>{t}Name{/t}</th>
					<th>{t}Version{/t}</th>
					<th>{t}Type{/t}</th>
					<th>{t}Description{/t}</th>
					<th>{t}Action{/t}</th>
				</tr>
			</thead>
			
			{if $blocList != null}
				<tbody>
					{foreach from=$blocList item=item}
						<tr>
							<td>{$item.name}</td>
							<td>{$item.version}</td>
							<td>{$item.type}</td>
							<td>{if isset($item.description)}{$item.description}{/if}</td>
							<td>
								<a href="#" data-name="{$item.name}" class="changeStatBloc btn btn-mini {if $item.load}btn-danger{else}btn-success{/if}" data-original-title="{if $item.load}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
									<i class="icon-off icon-white"></i>
								</a>
								
								<a href="{routeShort action="uninstall" id=$item.name type=$item.type}" class="btn btn-mini btn-danger" data-original-title="{t}Uninstall{/t}"
								onClick="return confirm('{t}Are you sure to delete this bloc ?{/t}')">
									<i class="icon-trash icon-white"></i>
								</a>
							</td>
						</tr>
					 {/foreach}
				 </tbody>
			{/if}								
		</table>
	</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Plugin List{/t} <span class="helper"></span></h2>
		<div>{t}Plugin List{/t}</div>
	</div>

	<div>
		<table class="datatable table table-bordered table-striped show_tooltip">
			<thead>
				<tr>
					<th>{t}Name{/t}</th>
					<th>{t}Version{/t}</th>
					<th>{t}Type{/t}</th>
					<th>{t}Description{/t}</th>
					<th>{t}Action{/t}</th>
				</tr>
			</thead>
			
			{if $pluginList != null}
				<tbody>
					{foreach from=$pluginList item=item}
						<tr>
							<td>{$item.name}</td>
							
							<td>{$item.version}</td>
							<td>{$item.type}</td>
							<td>{if isset($item.description)}{$item.description}{/if}</td>
							<td>
								<a href="{routeShort action="editplugin" id=$item.file type=$item.type}" class="changeStatPlugin btn btn-mini {if $item.active}btn-danger{else}btn-success{/if}" data-type="{$item.type}" data-name="{$item.name}" data-original-title="{if $item.active}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
									<i class="icon-off icon-white"></i>
								</a>
								<a href="{routeShort action="uninstall" id=$item.name type=$item.type}" class="btn btn-mini btn-danger" data-original-title="{t}Uninstall{/t}"
								onClick="return confirm('{t}Are you sure to delete this plugin ?{/t}')">
									<i class="icon-trash icon-white"></i>
								</a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			{/if}
		</table>
	</div>
	
	<div>
		<a href='{routeShort action="editplugin"}' class="btn btn-primary"><div><i class="icon-refresh icon-white"></i> {t}Update Plugins Config{/t}</div></a>
	</div>
	
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}CMS Plugin List{/t} <span class="helper"></span></h2>
		<div>{t}CMS Plugin List{/t}</div>
	</div>

	<div>
		<table class="datatable table table-bordered table-striped show_tooltip">
			<thead>
				<tr>
					<th>{t}Name{/t}</th>
					<th>{t}Classic{/t}</th>
					<th>{t}Api{/t}</th>
				</tr>
			</thead>
			
			{if $CMSpluginList != null}
				<tbody>
					{foreach from=$CMSpluginList key=key item=item}
						
							<tr>
								<td>{$item['name']}</td>
								<td>
									<a href="{routeShort action='editcmsplugins' id=$item['name'] type='classic'}" class="btn btn-mini {if $item['activeClassic']}btn-danger{else}btn-success{/if}" data-type="{$item.type}" data-name="{$item.name}" data-original-title="{if $item['activeClassic']}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
										<i class="icon-off icon-white"></i>
									</a>
								</td>
								<td>
									<a href="{routeShort action='editcmsplugins' id=$item['name'] type='api'}" class="btn btn-mini {if $item['activeApi']}btn-danger{else}btn-success{/if}" data-type="{$item.type}" data-name="{$item.name}" data-original-title="{if $item['activeApi']}{t}Disable{/t}{else}{t}Enable{/t}{/if}">
										<i class="icon-off icon-white"></i>
									</a>
								</td>
							</tr>
					 {/foreach}
				</tbody>
			{/if}
		</table>
	</div>
</div>
	
<div class="zone">
	<div class="zone_titre">
		<h2>{t}Install a new Package{/t} <span class="helper"></span></h2>
		<div>{t}Install a new Package{/t}</div>
	</div>
	
	{$form}
</div>		


{if $backAcl->hasPermission("mod_packager", "manage")}

<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>Choisissez vos options</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Rights{/t}</h2>
			<div>{t}Manage rights{/t}</div>
		</div>
		<div class="droits_content">
			
			<form action="{$formAcl->getAction()}" method="post"> 
			{$formAcl}
			<ul class="unstyled">
				<li><span class="bleu">Manage :</span> éditer les droits</li>
				<li><span class="bleu">View :</span> voir le module et son contenu</li>
				<li><span class="bleu">Create :</span> créer de nouveaux articles</li>
			</ul>
			<div class="droits_submit">
				{$formAcl->submit}
			</div>
			</form> 
			
		</div>
	</div>

{/if}	


{literal}
<script type="text/javascript">
$(document).ready(function() {	

	$('.changeStatPlugin').on('click', function(){
		$.ajaxCMS({
			url : "/packager/package/change-stat-plugin", 
			datas : {
				pluginFile : $(this).data('name'),
				type : $(this).data('type')
			},
			success : function(e){
				if (!e.error) {
					$('.changeStatPlugin[data-name="' + e.pluginFile + '"]').parent().effect("highlight", {}, 1000);		
					if (e.actualStat == 'disable'){
						$('.changeStatPlugin[data-name="' + e.pluginFile + '"]').removeClass('btn-danger').addClass('btn-success').attr('data-original-title', {/literal}'{t}Enable{/t}'{literal});
					}else if (e.actualStat == 'enable')
						$('.changeStatPlugin[data-name="' + e.pluginFile + '"]').removeClass('btn-success').addClass('btn-danger').attr('data-original-title', {/literal}'{t}Disable{/t}'{literal});
				}
			}
		});
		return false;
	});
	
	$('.changeStatModule').on('click', function(){
		$.ajaxCMS({
			url : "/packager/package/change-stat-module", 
			datas : {
				name : $(this).data('name')
			},
			success : function(e){
				if (!e.error) {
					$('.changeStatModule[data-name="' + e.moduleName + '"]').parent().effect("highlight", {}, 1000);		
					if (e.actualStat == 'disable'){
						$('.changeStatModule[data-name="' + e.moduleName + '"]').removeClass('btn-danger').addClass('btn-success').attr('data-original-title', {/literal}'{t}Enable{/t}'{literal});
					}else if (e.actualStat == 'enable')
						$('.changeStatModule[data-name="' + e.moduleName + '"]').removeClass('btn-success').addClass('btn-danger').attr('data-original-title', {/literal}'{t}Disable{/t}'{literal});
				}
			}
		});
		return false;
	});
	
	$('.changeStatBloc').on('click', function(){
		$.ajaxCMS({
			url : "/packager/package/change-stat-bloc", 
			datas : {
				name : $(this).data('name')
			},
			success : function(e){
				if (!e.error) {
					$('.changeStatBloc[data-name="' + e.blocName + '"]').parent().effect("highlight", {}, 1000);		
					if (e.actualStat == 'disable'){
						$('.changeStatBloc[data-name="' + e.blocName + '"]').removeClass('btn-danger').addClass('btn-success').attr('data-original-title', {/literal}'{t}Enable{/t}'{literal});
					}else if (e.actualStat == 'enable')
						$('.changeStatBloc[data-name="' + e.blocName + '"]').removeClass('btn-success').addClass('btn-danger').attr('data-original-title', {/literal}'{t}Disable{/t}'{literal});
				} 
			}
		});
		return false;
	});
});
</script>
{/literal}
