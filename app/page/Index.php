<?php

class Index {

    public static function run() {

        /*Example PDO/Active Record database interaction
        $results = DB::dbh()->query("SELECT * FROM element_test");

        foreach ($results as $row) {
            $record = new Record("element_test", $row["id"]);
        }

        $record->publish_name = "bigface";

        $record->save();
        $record->delete();*/
        Page::addXML($xml);
        Page::$xsl = ROOT."app/xsl/index.xsl";

        //Factory::rebuild();
    }

}
?>
