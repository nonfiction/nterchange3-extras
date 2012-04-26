Bootstrap for nterchange
========================

The only significant changes to the original https://github.com/twitter/bootstrap/
are the removal of the docs/ folder and modifications to the Makefile so the
compiled files get put in the right place.

Source is located at: public_html/stylesheets/bootstrap-src

The Makefile will build these files:

    stylesheets/bootstrap*.css
    javascripts/bootstrap*.js

I've also added two scripts to do the Make locally and push the results to your
remote host. Be sure to modify the first three lines in each to suit your project:

    $ cd public_html/stylesheets/bootstrap-src
    $ ./compile                 # compile less and copy result to remote
    $ ./sync                    # compile less and copy css+less sources to remote