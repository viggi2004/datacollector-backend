<?php

require_once("UISchema.php");
$ui_schema = new UISchema("restaurants");
$ui_schema = $ui_schema->generate();
echo json_encode($ui_schema);

?>
