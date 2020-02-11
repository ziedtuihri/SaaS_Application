# Conversion of a MySQL App to Multi-Tenant
<pre>
Let me first start by saying that the technique described below was not invented by me.
the original source is : <a href="https://opensource.io/it/mysql-multi-tenant/" target="_blank">here </a> https://opensource.io/it/mysql-multi-tenant/ 
</pre>
<h3 style="color : red;">in my file Generate_multiTanentMysql.php i do all steps in opensource.io en PHP script</h3>

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
Then, the only part of the application that needs to change is the database connection logic. When someone connects to the SaaS, the application would need to:
<li>Connect to the database as that tenant-specific username</li>
</ul>
<h3>Background</h3>
<pre>
Many years ago I was faced with the challenge to take a legacy web application intended for one 
customer (tenant) and turn itinto a multi-tenant SaaS application. 
The app itself was large, but not particularly complex. It used around 60 tables
in a MySQL database and did not use views,triggers, or stored procedures. 
At the start, the application was accessible using a single domain.
The request was to alloweach “tenant” to access the software from a different domain without any 
sharing of data.
or my solutions Zied Tuihri is with a session with the name of tenant to connect with the 
string value in database .
</pre>
