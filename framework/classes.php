<?php

/////////////////////////////////////////////////////////////////////////////////////
// Setup classes here                                                              //
/////////////////////////////////////////////////////////////////////////////////////

//core
Factory::add("FrameEx","framework/core/FrameEx.php");
Factory::add("Registry","framework/core/Registry.php");

//database
Factory::add("DB","framework/database/DB.php");
Factory::add("Record","framework/database/Record.php");
Factory::add("MissingRecord","framework/database/MissingRecord.php");
Factory::add("MultipleRecord","framework/database/MultipleRecord.php");

//display
Factory::add("Page","framework/display/Page.php");
Factory::add("XML","framework/display/XML.php");
Factory::add("MalformedPage","framework/display/MalformedPage.php");

//event
Factory::add("Event","framework/event/Event.php");
Factory::add("UnknownEvent","framework/event/UnknownEvent.php");
Factory::add("Dispatcher","framework/event/Dispatcher.php");

//util
Factory::add("ArrayUtil","framework/util/ArrayUtil.php");

?>