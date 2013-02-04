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
		<div class="jobdetails">
				<h1>Détail de l'offre: {$job->job_title}</h1>
			<div class="document_content">
			
				<div class="job_resume">
					<div class="job_title">
						<h2>Poste :</h2>
						<p>{$job->job_title}</p>
					</div>
					<div class="job_contract">
						<h2>Type de contrat : </h2>
						<p>{$job->contract_type}</p>
					</div>
					<div class="job_sector">
						<h2>Secteur Géographique : </h2>
						<p>{$job->sector}</p>
					</div>
					<div class="job_desc">
						<h2>Description du poste :</h2>
						<p>{$job->description}</p>
					</div>
					
					<p class="read_more">
						>>> <a href="{routeFull route="jobs" action="apply" id=$job->id}">Postuler</a>
					</p>
					
					<p class="back_joblist">
						>>> <a href="{routeFull route="jobs"}">Retour à la liste des annonces</a>
					</p>
			
				</div>
			
			</div>
	</div>
	<div class="document_content_bottom"></div>
</div>
