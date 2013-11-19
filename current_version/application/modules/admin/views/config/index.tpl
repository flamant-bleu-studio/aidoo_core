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
		<h2>{t}Mobile{/t}</h2>
		<div>{t}Mobile configuration{/t}</div>
	</div>	
	
	<form action='{$mobileForm->getAction()}' method="post" id="{$mobileForm->getId()}" >
		{$mobileForm->mobile}
		{$mobileForm->tablet}
		<div class="form_submit">
			<button class="btn btn-primary">{t}Save{/t}</button>
		</div>
	</form>
	
	<div class="zone_titre">
		<h2>{t}Api Key{/t}</h2>
		<div>{t}Server rest configuration{/t}</div>
	</div>	
	

	<div>{t}Your actual Key is : {/t}{$apiKey}</div><br />
	<a href='{routeShort action="generateapikey"}' class="btn btn-warning btn-large">{t}Generate new Api Key{/t}</a>
	<br /><br />

	<div class="zone_titre">
		<h2>{t}Log{/t}</h2>
		<div>{t}Log configuration{/t}</div>
	</div>
	
	<form class="log" action='{$logForm->getAction()}' method="post" id="{$logForm->getId()}" >
		<div class="well">
			<div data-real-type="stream" class="left">{$logForm->log_stream}</div>
			<div data-real-type="stream" class="left config">{$logForm->log_stream_min_level}</div>
		</div>
		
		<div class="well">
			<div data-real-type="mail" class="left">{$logForm->log_mail}</div>
			<div data-real-type="mail" class="left config">
				<br/>
				<div class="left">{$logForm->log_mail_min_level}</div>
				<div class="left">{$logForm->log_mail_to}</div>
			</div>
			<div class="clearfix"></div>
		</div>
	
		<div class="well">
			<div data-real-type="db" class="left">{$logForm->log_db}</div>
			<div data-real-type="db" class="left config">{$logForm->log_db_min_level}</div>
		</div>	
		
		<div class="well">
			<div data-real-type="firebug" class="left">{$logForm->log_firebug}</div>
			<div data-real-type="firebug" class="left config">{$logForm->log_firebug_min_level}</div>
		</div>
		
		<div class="form_submit">
			<button class="btn btn-primary">{t}Save{/t}</button>
		</div>
	</form>
	
	<div class="zone_titre">
		<h2>{t}Caches{/t}</h2>
	</div>	

	<a href='{routeShort action="deletecache"}' class="btn btn-primary btn-large">{t}Clear CSS & Javascript cache{/t}</a>
	<a href='{routeShort action="clear-cache-tpl"}' class="btn btn-primary btn-large">{t}Clear templates cache{/t} </a>
	<br /><br />
	
	<div class="zone_titre">
		<h2>{t}Contents{/t}</h2>
		<div>{t}Manage site contents{/t}</div>
	</div>	
	
	<a href='{routeShort action="confirmresetcontent"}' class="btn btn-danger btn-large">{t}Reset all site content{/t}</a>

	<br /><br />
	<div class="zone_titre">
		<h2>{t}Maintenance{/t}</h2>
		<div>{t}Enable / disable maintenance{/t}</div>
	</div>	
	
	{if $maintenance == 1}
		<a href='{routeShort action="maintenance" id=0}' class="btn btn-danger btn-large">{t}Disable maintenance{/t}</a>
	{else}
		<a href='{routeShort action="maintenance" id=1}' class="btn btn-danger btn-large">{t}Enable maintenance{/t}</a>
	{/if}

	<br /><br />
	<div class="zone_titre">
		<h2>{t}Rights{/t}</h2>
		<div>{t}Manage admin rights{/t}</div>
	</div>

	<div class="droits_content">
		
		<form action="{$formAcl->getAction()}" method="post" id="form-size"> 
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

<script type="text/javascript">
$(document).ready(function(){

	var counter = '{$test + 1}';
	
	$("#add-size").on("click", function(e){
		e.preventDefault();

		var line = $(this).parents("form").find(".form-inline:first").clone();
		
		var name = line.find(".name");
		var width = line.find(".width");
		var height = line.find(".height");
		var adaptiveResize = line.find(".adaptiveResize");
		
		name.attr("name", name.attr("name").replace("0", counter)).removeAttr("readonly").val("");
		width.attr("name", width.attr("name").replace("0", counter)).val("");
		height.attr("name", height.attr("name").replace("0", counter)).val("");
		adaptiveResize.attr("name", adaptiveResize.attr("name").replace("0", counter)).removeAttr('checked');
		
		$(this).before(line);
		counter++;
	});
	
	$(".delete-size").on("click", function(e){
		e.preventDefault();
		$(this).parents(".form-inline").remove();
	});
	
	/**
	 * Affiche / Cache la config d'un type de log suivant l'état du log à l'init de la page
	 */
	$('form.log input[type="checkbox"]').each(function(index, element){
		var is_checked = $(this).attr('checked') ? true : false;
		
		if (!is_checked)
			$('form.log div.config').eq(index).hide();
	});
	
	/**
	 * Affiche / Cache la config d'un type de log suivant l'état du log lors d'un changement
	 * Affiché si activé
	 * Caché si désactivé
	 */
	$('form.log').on('change', 'input[type="checkbox"]', function(){
		var type = $(this).parent().parent().parent().data('real-type');
		
		$('form.log div[data-real-type="'+ type +'"].config').toggle();
	});
});
</script>
