<h1>{t}Packages{/t}</h1>

<ul class="nav nav-tabs" id="nav-packages">
	<li {if $active == 'modules'}class="active"{/if}><a href="{routeShort action="modules"}">{t}Modules{/t}</a></li>
	<li {if $active == 'blocs'}class="active"{/if}><a href="{routeShort action="blocs"}">{t}Blocs{/t}</a></li>
	<li {if $active == 'plugins'}class="active"{/if}><a href="{routeShort action="plugins"}">{t}Plugins{/t}</a></li>
	<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
</ul>