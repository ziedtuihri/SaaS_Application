# Conversion of a MySQL App to Multi-Tenant
<pre>
Let me first start by saying that the technique described below was not invented by me.
the original source is : <a href="https://opensource.io/it/mysql-multi-tenant/" target="_blank">here </a>
</pre>
<h3 style="color : red;"> A Solution Design Pattern </h3>
<pre>
<ul>
<h5>The chosen solution involves the following:</h5>
<li>Creating a database user for each tenant </li>
<li>Renaming every table to a different and unique name (e.g. using a prefix ‘someprefix_’) </li>
<li>Adding a text column called ‘id_tenant’ to every table to store the name of the tenant the row belongs to </li>
<li> Creating a trigger for each table to automatically store the current database username to the id_tenant column before inserting a new row</li>
<li>Creating a view for each table with the original table name with all the columns except id_tenant. The view will only return rows where (id_tenant = current_database_username) </li>
<li> Only grant permission to the views (not tables) to each tenant’s database user</li>
</ul>
</pre>
