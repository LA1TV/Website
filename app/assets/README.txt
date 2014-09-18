These "scripts" and "css" folders should be dynamically routed so they appear in the "../../public/assets" folder. I.e. accessible at /assets/scripts and /assets/css

In the homestead environment (https://github.com/LA1TV/Website-Homestead) this is set up in nginx automatically by the provision script.
The same can be achieved in apache with mod_rewrite rules.

I have set it up like this so that on the production server the assets can easily be processed (eg mimified and compressed) and outputted somewhere else, and then mapped from there.

"build-scripts" contains the config files for r.js (https://github.com/jrburke/r.js) nodejs application to generate compressed verisons of the js and css files. These are outputted in "builds" which is excluded from version control.