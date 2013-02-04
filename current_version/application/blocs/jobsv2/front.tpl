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

<div class="bloc_jobs">
	{if $jobs|@count > 0}
	
		{foreach from=$jobs item=a}
			
			<div class="bloc_jobs_job">
				<h3>Poste : {$a->job_title}</h3>
				<p>Type de contrat : {$a->contract_type}</p>
				<p>Secteur géographique : {$a->sector}</p>
				<div class="bloc_jobs_description">
					<p>Description du poste : {$a->description}</p>
				</div>
				<div class="bloc_jobs_url">
					<a href='{routeFull route="jobs" action="view" id="{$a->id}"}'>{t}View job{/t}</a>
				</div>
			</div>
			
		{/foreach}
		
		<a class="bloc_jobs_readmore" href='{routeFull route="jobs"}'>{t}Read more{/t}</a>
		
	{else}
		<h3>{t}No job to display{/t}</h3>
	{/if}
</div>
