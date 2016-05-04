About
-----

POETEVAL is a simple PHP program designed to to parse a Moodle plugin to create the travis.yml file
@author    Derek Henderson <derek.henderson@remote-learner.net>
@copyright 2016 Remote-Learner, Inc.

# Introduction

This project is a simple PHP program designed to parse a Moodle plugin and create the travis.yml for submission to travis.ci.


# Install

Just download it to your local machine.
# Usage

Usage: PHP poeteval.php  [-h |--help]
               [--folder= path to folder]
               [--moodle= Specify the Moodle version. Supported versions 27, 28, 29, 30]
               [--php= Specify the PHP version. Supported versions 5.4, 5.5, 5.6, 7.0 
               [--db= Specify the database mysqli - (default) | pgsqli ]'


# Credits

All praise should go to the contributors of
[moodle-local_codechecker](https://github.com/moodlehq/moodle-local_codechecker).

# License

This project is licensed under the GNU GPL v3 or later.  See the [LICENSE](LICENSE) file for details.
