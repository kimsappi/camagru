<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require_once($templates_path . "head.php");
require_once($templates_path . "header.php");
?>
<button onclick="window.location.href = '/take_pic.php';">Snap!</button>
<?php
require_once($templates_path . "footer.php");
?>