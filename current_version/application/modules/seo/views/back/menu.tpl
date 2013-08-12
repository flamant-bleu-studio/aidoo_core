<h1>{t}SEO{/t}</h1>

<ul class="nav nav-tabs" id="nav-menu">
	<li {if $active == 'pages'}class="active"{/if}><a href="{routeShort action="pages"}">{t}Pages{/t}</a></li>
	<li {if $active == 'types'}class="active"{/if}><a href="{routeShort action="types"}">{t}Types{/t}</a></li>
	<li {if $active == 'socials'}class="active"{/if}><a href="{routeShort action="socials"}">{t}Socials{/t}</a></li>
	<li {if $active == 'config'}class="active"{/if}><a href="{routeShort action="config"}">{t}Configuration{/t}</a></li>
	{if $backAcl->hasPermission('mod_seo', "manage")}
		<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
	{/if}
</ul>