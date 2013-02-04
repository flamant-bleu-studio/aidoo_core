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

<div id="error">
	
	{if isset($exception)}
		<h3>Exception information:</h3>
		<p><strong>Message:</strong> {$exception->getMessage()}</p>
		<h3>Stack trace:</h3>
		<pre>{$exception->getTraceAsString()}</pre>
		
		<h3>Request Parameters:</h3>
		<pre>{$dump_request}</pre>
	{else}
		<p><img src="{$baseUrl}/images/img_404.jpg" /></p>
		<script type="text/javascript">
			window.setTimeout("location=('http://{$smarty.server.SERVER_NAME}{$baseUrl}');",5000);
		</script>
	{/if}
</div>
