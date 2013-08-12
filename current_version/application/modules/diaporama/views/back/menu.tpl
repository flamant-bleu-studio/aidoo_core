<h1>{t}Slideshows{/t}</h1>

<ul class="nav nav-tabs" id="nav-article">
	<li {if $active == 'diaporamas'}class="active"{/if}><a href="{routeShort action="index"}">{t}Slideshows{/t}</a></li>
	{if $backAcl->hasPermission('mod_diaporama', "manage")}
		<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Persmissions{/t}</a></li>
	{/if}
	{if $active == 'create'}<li class="active"><a>{t}Creating a new slideshow{/t}</a></li>{/if}
	{if $active == 'edit'}<li class="active"><a>{t}Editing a slideshow{/t}</a></li>{/if}
</ul>