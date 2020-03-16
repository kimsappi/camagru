<?php
$head_title = "Camagru";
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/head.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/header.php");
?>
<button onclick="window.location.href = '/take_pic.php';">Snap!</button>
<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/templates/footer.php");
?>