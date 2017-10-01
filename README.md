Website
=======

LA1TV's website (https://www.la1tv.co.uk/).

Uses the java Website Upload Processor at "https://github.com/LA1TV/Website-Upload-Processor/"

There is a repository for the development environment which uses [Vagrant](
https://www.vagrantup.com/) at "https://github.com/LA1TV/Website-Homestead".

"app/storage" is excluded from version control and should contain the following folders:
- cache
- file_chunks
- files
- logs
- meta
- sessions
- views

You also need to run the laravel queue listener on the default queue, and also "uploadTransfer" and "smartCache" queues. The homestead environment handles this for you.

# Assets
The javascript and css is all built using webpack, and is managed with npm. So first install npm, then run `npm install` to get all the dependencies.

They are split up into "home" for the main front end, "admin" for the CMS, and "embed".

To build the assets and automatically rebuild when you make changes use:
- `npm run watchHome`
- `npm run watchAdmin`
- `npm run watchEmbed`
