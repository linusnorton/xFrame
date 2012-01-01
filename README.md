PHP xFrame
==========

A lightweight MVC framework

Features
--------

* Incredibly fast (boot in 2.2ms)
* Simple autoloading
* Dependency injection container
* Annotation based request mapping
* Phing build, test and documentation scripts
* Multiple view types: Twig (default), PHPTAL, pure PHP
* Inbuilt caching
* Doctrine2 integration (optional)

Installation
------------

Pear installation

    $ sudo pear config-set auto_discover 1
    $ sudo pear install pear.linusnorton.co.uk/xFrame

Install with Doctrine2 (Optional)

    $ sudo pear config-set auto_discover 1
    $ sudo pear install --alldeps pear.linusnorton.co.uk/xFrame

(Note that PHP5.3 is required and APC is recommended)

Setup
-----

Create directory structure

    $ xframe --create-project /var/www/demo

Ensure you have created your [virtual host](https://github.com/linusnorton/xFrame/wiki/Example-virtual-host), enabled mod_rewrite, restarted apache and edited /etc/hosts if necessary

Getting Started
---------------

Enter the domain you entered in the virtual host and test the set up was successful.

* Start hacking `src/demo/controller/Index.php` and `view/index.html`
* [read about request mapping](https://github.com/linusnorton/xFrame/wiki/Request-Mapping)
* [read about the dependency injection container](https://github.com/linusnorton/xFrame/wiki/Dependency-Injection-Container)
* [read about bootstrapping](https://github.com/linusnorton/xFrame/wiki/Bootstrap)
* [read about Doctrine2 integration](https://github.com/linusnorton/xFrame/wiki/Doctrine2-Integration)
* [read about the testing and the phing script](https://github.com/linusnorton/xFrame/wiki/Using-the-Phing-Script)
* [read about adding CLI targets](https://github.com/linusnorton/xFrame/wiki/Creating-CLI-Targets)
* [read about the exception mailer](https://github.com/linusnorton/xFrame/wiki/Exception-Mailer)