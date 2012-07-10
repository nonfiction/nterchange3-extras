nterchange3-extras
==================

Assets, frontend boilerplate and frameworks to mix into nterchange.

Each addon lives in a folder that mimics the directory structure of
the front end. Each folder should contain a README with any
instructions for intent, installation and use.

Setup
------------------

Change target host in config.py to the server address where you want to
deploy to.

Installing add-ons
------------------

This is all there is to it:

    $ ./deploy -c your_site name_of_folder

You can also target a host other than the one in config.py with the -t option or specify
a different username (other than `whoami`) on the remote with the -u option.
`./deploy --help` for more info.

Building new add-ons
--------------------

Just create a new folder with a descriptive title, and add the directories and
files that should be added/changed to the nterchange folder on the target server.
Be sure to note in the README any files that may overwrite existing files. In 
general, do not include files like stylesheets/default.css or other distribution
files, just include a file with the recommended changes to those files and a note
in the README about what needs to be done (eg: stylesheets/default.your_add_on.css)

**Note on permissions** - since git doesn't preserve folder permissions the
deploy script changes all file/folder permissions to 775 or `rwxrwxr-x`. This
should cover most cases, unless your writing to the /var or public_html/uploads
folders. If you are, make a note of it in the README.
