<?php

////////////////////////////////////////////////////////////////////////////////
// Setup resource to controller mapping here                                  //
////////////////////////////////////////////////////////////////////////////////

//resource name, class, method, [cache length], [param mapping], [authenticator]
Dispatcher::addListener("home", "Index", "run");

//if you want to load resource from the database you can do so like this:
//Resource::loadFromDB("resource");

