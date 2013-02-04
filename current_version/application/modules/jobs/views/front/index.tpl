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

<div class="document">
	<h1>Nos offres</h1>
	<div class="document_content">
		<form name="filterjobs" action="{$smarty.server.REQUEST_URI} " method="POST" >
			<input type="hidden" name="filter" />
			<div class="line_formjobs">
				<label for="contractTypeList" id="contract_type">Type de contrats : </label>
				<select name="contractType" onchange="submit();">
					<option value=''>Tous</option>
					{html_options output=$contractTypeList values=$contractTypeList selected=$selectedContractType}
				</select>
				<label for="sectorList" id="sector_job">Secteur : </label>
				<select name="sector" onchange="submit();">
					<option value=''>Tous</option>
					{html_options output=$sectorList values=$sectorList selected=$selectedSector}
				</select>
			</div>
			<div class="line_formjobs">
				<label for="domainList" id="domaine_job">Domaine : </label>
				<select name="domain" onchange="submit();">
					<option value=''>Tous</option>
					{html_options output=$domainList values=$domainList selected=$selectedDomain}
				</select>
			</div>
		</form>	

		<br/>

		<div class="jobs_liste">
		{if $jobs}
			{foreach from=$jobs key=key item=item}
			<div class="job_resume">
					<div class="job_title">
						<h2>{$item->job_title}</h2>
					</div>
					<div class="job_contract">
						<p>{$item->contract_type} - {$item->sector}</p>
					</div>
					
					<p class="read_more">
						<a href="{routeFull route="jobs" action="view" id=$item->id}">{t}Read more{/t}...</a>
					</p>
					
					<p class="read_more">
						<a href="{routeFull route="jobs" action="apply" id=$item->id}">{t}Apply for this job{/t}</a>
					</p>
			
			</div>
			{/foreach}
		{else}
			<p> Aucune annonce.</p>
		{/if}
		</div>
	</div>
	<div class="document_content_bottom"></div>
</div>
