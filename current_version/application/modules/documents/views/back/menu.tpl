<h1>{t}Documents{/t}</h1>

<ul class="nav nav-tabs" id="nav-docs">
	<li {if $active == 'pages'}class="active"{/if}><a href="{routeShort action="index"}">{t}Documents{/t}</a></li>
	{if $backAcl->hasPermission('mod_documents', "manage")}
		<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
	{/if}
	{if $active == 'new-pages'}<li class="active"><a>{t}New documents{/t}</a></li>{/if}
	{if $active == 'edit'}<li class="active"><a>{t}Edit document{/t}</a></li>{/if}
</ul>