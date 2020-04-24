<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/require.php");
require($functions_path . "dbConnect.php");
require_once($functions_path . "utils.php");

class Comment {
	public function __construct($commentData) {
		if ($connection = dbConnect())
			$this->render = TRUE;
		else {
			$this->render = FALSE;
			return;
		}
		$this->commentText = $commentData['content'];

		$userDataQuery = <<<EOD
		SELECT `username` FROM `users`
			WHERE `id` = ?;
EOD;
		$userDataQuery = $connection->prepare($userDataQuery);
		$userData = $userDataQuery->execute([$commentData['user_id']]);
		if (!$userData)
			$this->render = FALSE;
		else
			$this->userName = $userDataQuery->fetch(PDO::FETCH_ASSOC)['username'];
	}

	public function __toString() {
		if (!$this->render)
			return '';
		$userName = sanitiseOutput($this->userName);
		$text = sanitiseOutput($this->commentText);
		return <<<EOD
			<div class='commentContainer'>
				<div class='commentUsername'>$userName</div>
				<div class='commentContent'>$text</div>
			</div>
EOD;
	}
}
