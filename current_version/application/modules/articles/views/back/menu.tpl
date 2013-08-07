<h1>{t}Articles{/t}</h1>

<ul class="nav nav-tabs" id="nav-article">
	<li {if $active == 'articles'}class="active"{/if}><a href="{routeShort action="articles"}">{t}Articles{/t}</a></li>
	<li {if $active == 'categories'}class="active"{/if}><a href="{routeShort action="categories"}">{t}Categories{/t}</a></li>
	{if $active == 'new'}<li class="active"><a>{t}New article{/t}</a></li>{/if}
	{if $active == 'edit'}<li class="active"><a>{t}Edit article{/t}</a></li>{/if}
</ul>