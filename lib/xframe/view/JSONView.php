<?php

namespace xframe\view;
use \xframe\registry\Registry;

/**
 * JSONView is the view for outputting json
 */
class JSONView extends View {

    /**
     * Generate the JSON
     * @return string
     */
    public function execute() {
        return json_encode($this->parameters);
    }

}
