<h1>{t}Blocs & Disposition{/t}</h1>

<ul class="nav nav-tabs" id="nav-docs">
	<li {if $active == 'templates'}class="active"{/if}><a href="{routeShort action="index"}">{t}Templates{/t}</a></li>
	<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
	{if $active == 'create'}<li {if $active == 'create'}class="active"{/if}><a>{t}Create bloc{/t}</a></li>{/if}
</ul>