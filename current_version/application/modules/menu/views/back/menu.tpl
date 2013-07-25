<h1>{t}Manage menu{/t}</h1>

<ul class="nav nav-tabs" id="nav-menu">
	<li {if $active == 'menus'}class="active"{/if}><a href="{routeShort action="index"}">{t}Menus{/t}</a></li>
	<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
</ul>