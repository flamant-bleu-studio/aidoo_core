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

{appendFile type="jquery" src=$skinUrl+"/js/flot/jquery.flot.js"}	

{if $flot_data_visits != null}
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var visits = {$flot_data_visits};
	var views = {$flot_data_views};
	$('#placeholder').css({
		height: '300px',
		width: '450px'
	});
	$.plot($('#placeholder'),[
    	{ label: 'Nombre de visites', data: visits },
    	{ label: 'Nombre de pages vues', data: views }
    	],{
          	lines: { show: true },
          	points: { show: true },
          	grid: { backgroundColor: '#fffaff' }
  	});
});
</script>
{/if}

<div class="content_titre">
	<h1>{t}Statistiques de votre site{/t}</h1>
	<div>Consultez les statistiques d'activité de votre site</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Statistiques Google Analytics pour le site : {$site} {/t}<span class="helper"></span></h2>
		<div>Données Google Analytics</div>
	</div>
	
	{if $month != null}
	<form method="get">
		<select id="month" name="month">
			<option value="">-- Sélectionner le mois --</option>
			<option value="1">Janvier</option>
			<option value="2">Février</option>
			<option value="3">Mars</option>
			<option value="4">Avril</option>
			<option value="5">Mai</option>
			<option value="6">Juin</option>
			<option value="7">Juillet</option>
			<option value="8">Août</option>
			<option value="9">Septembre</option>
			<option value="10">Octobre</option>
			<option value="11">Novembre</option>
			<option value="12">Décembre</option>
		</select>
		<select id="year" name="year">
			<option value="">-- Sélectionner l'année --</option>
			<option value="2008">2008</option>
			<option value="2009">2009</option>
			<option value="2010">2010</option>
			<option value="2011">2011</option>
		</select>
		<input type="submit" name="submit" value="Obtenir les statistiques" class="btn_vert" />	
	</form>
	
	<br/>
	
	<div class="form_line">
		<div class="form_text">
			<ul>
				<li style="font-weight: bold">Données Google Analytics pour la période {$start} / {$end} :</li>
				<li>&nbsp;</li>
				<li>{$visits} visites</li>
				<li>{$pageviews} pages vues</li>
				<li>{$pageviewsPerVisit} pages / visites</li>
				<li>{$timeOnSite} temps / visite</li>
			</ul>
		</div>
		<div class="form_elem" id="placeholder"></div>
		<br/>
		<div class="form_elem" style="font-weight: bold">Statistiques {$month}/{$year}</div>
	</div>
	{else}
	<div class="form_line">
		<div id="stats_error">Pas de compte Google Anlaytics associé</div>
	</div>
	{/if}
	
	<div class="clear"></div>
</div>
	
{if $backAcl->hasPermission("mod_statistics", "view")}

	<div class="content_titre">
		<h1>{t}Options{/t}</h1>
		<div>Choisissez vos options</div>
	</div>
	
	<div class="zone">
		<div class="zone_titre">
			<h2>{t}Module Rights{/t}</h2>
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
