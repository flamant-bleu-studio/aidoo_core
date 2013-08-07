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
	<h1>{t}Edit site configuration{/t}</h1>
	<div>Configuration du site</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Front office languages{/t}</h2>
		<div>{t}Configure languages{/t}</div>
	</div>	


	<table class="table table-bordered table-striped">
	
		<thead>
			<tr>
				<th></th>
				<th>Code</th>
				<th>Actions</th>
			</tr>
		</thead>
		
		<tbody>
			{foreach from=$frontLangs key=id item=lang}
			<tr>
				<td><img src="{$baseUrl}{$skinUrl}/images/flags/{$lang}.png" /></td>
				<td>{$lang}</td>
				<td>
					{if $id == $frontDefault}
						<button class="btn btn-mini disabled">
							{t}Default lang{/t}
						</button>
					{else}
						<a href='{routeShort controller="lang" action="default-front-lang" id=$lang}' class="btn btn-mini">
							{t}Change default lang{/t}
						</a>
					{/if}
					<a href='{routeShort controller="lang" action="delete-front-lang" id=$lang}' title="{t}Delete{/t}" class="btn btn-danger btn-mini">
						<i class="icon-trash icon-white"></i>
					</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form action='{routeShort controller="lang" action="addLanguage"}' method="post" class="form-inline">
	
		<input type="hidden" name="type" value="front">
		
		<label>{t}Add language from {/t}</label>
		
		<select name="from_lang_id" class="input-mini">
			{html_options options=$frontLangs}
		</select>
		
		<label>{t}to{/t}</label>
		
		<select name="lang">
			<option value="">{t}Choose the language to add{/t}</option>
			{html_options options=$liste}
		</select>
		
		<button type="submit" class="btn btn-success"><i class="icon-plus icon-white"></i>{t}Add{/t}</button>

	</form>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Back office languages{/t}</h2>
		<div>{t}Configure languages{/t}</div>
	</div>	

	<table class="table table-bordered table-striped">
	
		<thead>
			<tr>
				<th></th>
				<th>Code</th>
				<th>Actions</th>
			</tr>
		</thead>
		
		<tbody>
			{foreach from=$backLangs key=id item=lang}
			<tr>
				<td><img src="{$baseUrl}{$skinUrl}/images/flags/{$lang}.png" /></td>
				<td>{$lang}</td>
				<td>
					{if $id == $backDefault}
						<button class="btn btn-mini disabled">
							{t}Default lang{/t}
						</button>
					{else}
						<a href='{routeShort controller="lang" action="default-back-lang" id=$lang}' class="btn btn-mini">
							{t}Change default lang{/t}
						</a>
					{/if}
					<a href='{routeShort controller="lang" action="delete-back-lang" id=$lang}' title="{t}Delete{/t}" class="btn btn-danger btn-mini">
						<i class="icon-trash icon-white"></i>
					</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form action='{routeShort controller="lang" action="addLanguage"}' method="post" class="form-inline">
	
		<input type="hidden" name="type" value="back">
		
		<label>{t}Add language from{/t}</label>
		<select name="from_lang_id" class="input-mini">
			{html_options options=$backLangs}
		</select>
		
		<label>{t}to{/t}</label>
		<select name="lang">
			<option value="">{t}Choose the language to add{/t}</option>
			{html_options options=$liste}
		</select>
		
		<button type="submit" class="btn btn-success"><i class="icon-plus icon-white"></i>{t}Add{/t}</button>

	</form>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Configuration{/t}</h2>
		<div>{t}Options and translations tools{/t}</div>
	</div>	
	<a href='{routeShort controller="lang" action="check-files"}' class="btn btn-primary btn-large" >
		{t}Check languages files and permissions{/t}
	</a>
	<a href='{routeShort controller="lang" action="clean-cache-back"}' class="btn btn-primary btn-large" >
		{t}Clean back cache{/t}
	</a>
	<a href='{routeShort controller="lang" action="clean-translate-front-files"}' class="btn btn-warning btn-large" >
		{t}Regenerate front translations files{/t} (.po)
	</a>
	<a href='{routeShort controller="lang" action="generate-cached-translate-front-file"}' class="btn btn-warning btn-large" >
		{t}Regenerate front translations cache{/t} (.mo)
	</a>
</div>
