nterchange3-extras
==================

Assets, frontend boilerplate and frameworks to mix into nterchange.

Each addon lives in a folder that mimics the directory structure of
the nterchange front end. Each folder should contain a README with any
instructions for intent, installation and use.

Installing add-ons
------------------

New method. For example, to install bootstrap on pennwest site:
    
    $ ./deploy -c pennwest bootstrap

The *old* method is to tar/gzip the **contents** of the add-on you want to
install, copy it to your site's root and extract it:

    $ cd nterchange3-extras/bootstrap
    $ tar -czf bootstrap.tgz *
    $ cp bootstrap.tgz your_project_root/
    $ cd your_project_root/
    $ tar -xzf bootstrap.tgz

If the directory structure of the add-on matches nterchange this should put
everything in it's right place. If you end up with a folder named bootstrap/ 
in the project root, you've packaged the folder itself, not the contents. 

Building new add-ons
--------------------

Just create a new folder with a descriptive title, and add the directories and
files that should be added/changed to the nterchange folder on the target server.
Be sure to note in the README any files that may overwrite existing files. In 
general, do not include files like stylesheets/default.css or other distribution
files, just include a file with the recommended changes to those files with a note
in the README about what needs to be done (eg: stylesheets/default.your_add_on.css)

Before committing, be sure to chmod -R 775 the add-on folder, or you may break things!

  chmod -R 775 your_add_on