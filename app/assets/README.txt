The "builds" folder should be dynamically routed so it appears at /assets/built

The "service-workers" directory is used to store javascript service workers and files in here will be served by laravel.

In the homestead environment (https://github.com/LA1TV/Website-Homestead) this is set up in nginx automatically by the provision script.
The same can be achieved in apache with mod_rewrite rules.