<?php

/////////////////////////////////////////////////////////////////////////////////////
// Setup dispatcher methods here                                                   //
//                                                                                 //
// The parameter map is entirely optional, it enables you to use URLS like:        //
//                                                                                 //
// http://www.yourwebsite.com/search/chicken/asc/0/10                              //
//                                                                                 //
// mapping chicken to the search term, asc to a different param etc so when the    //
// event is passed to the handler you can access $event['searchTerm']              //
//                                                                                 //
/////////////////////////////////////////////////////////////////////////////////////

//Event name, class handler, method, cache length (optional), param mapping (optional)
$parameterMap = array("yourParamHere", "yourSecondParamHere");
Dispatcher::addListener("home", "Index", "run", 60, $parameterMap);

//simple version
//Dispatcher::addListener("Index", "Index", "run");


?>