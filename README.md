# O.R.K. - Online Record Keeper

This is version 3.5 of the ORK. It is a rewrite from the ground up, though it relies on the database schema from ORK 3 for its base structure.

## Laravel and Changes from ORK 3

This version of the ORK is built on top of [Laravel 5](https://www.laravel.com).

For now, extra things like the old code and APIs are still present. However, these are deprecated and will be removed in a future release.

## Setting up for development

This project uses [Vagrant](http://www.vagrantup.com) to build out a virtual development environment for you. Make sure you have it installed, and then just type the following command into your terminal from the project root:

```
vagrant up
```

This will set up a web server running Ubuntu 14.04 with PHP 7.0, NodeJS 6.x, MySQL 5.6, git, and memcached installed. 

It will create a database for the project the first time that it's run and sets up an admin user. By default, this admin user has the email "changeme@example.com" so you'll want to modify it to be your own. To do that, edit the `vagrant.yml` file's `admin_email` var to be your email.

The admin user has the username `admin` and the password `e01e44f3`.

Once you've started Vagrant, the next step is to pull down dependencies:

```
composer install
npm install
```

Those two commands will install all of the project's dependencies.

At this point, you can now visit `http://192.168.33.13/` in your browser and you should see the ORK home page. If you prefer, you can also modify your hosts file (`/etc/hosts` on *nix systems) to add the following line:

```
192.168.33.13  ork.vm
```

This gives you a slightly easier way to reference the site.
