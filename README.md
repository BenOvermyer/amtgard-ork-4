# O.R.K. - Online Record Keeper

## Setting up for development

This project uses [Vagrant](http://www.vagrantup.com) to build out a virtual development environment for you. Make sure you have it installed, and then just type the following command into your terminal from the project root:

```
vagrant up
```

This will set up a web server running Ubuntu 14.04 with PHP 5.5, MySQL 5.6, git, and vim installed. It will create a database for the project the first time that it's run and sets up an admin user. By default, this admin user has the email "changeme@example.com" so you'll want to modify it to be your own. To do that, edit the `vagrant.yml` file's `admin_email` var to be your email.

The admin user has the username `admin` and the password `e01e44f3`.

This will also place the `config.php` file required for the application to run.

At this point, you can now visit `http://192.168.33.13/orkui` in your browser and you should see the ORK home page. If you prefer, you can also modify your hosts file (`/etc/hosts` on *nix systems) to add the following line:

```
192.168.33.13  ork.vm
```

This gives you a slightly easier way to reference the site.
