<?php

class Helper_Tools {

    public static function requireFields($fields = []) {
        foreach ($fields as $field) {
            if (empty(\Flannel\Core\Input::find($field))) {
                return $field;
            }
        }

        return;
    }

}
