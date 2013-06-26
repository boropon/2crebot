<?php
	//ライブラリインクルード
	require_once("twitteroauth.php");
	
	//OAuthのキーを設定
	$consumer_key = "epZiUS66NYxESrtIy1Cg";
	$consumer_secret = "NhfD58UCb6QdE0aCV8PSIhcsGjcJNKuIX6zf3RPRfSI";
	$access_token = "452778967-FBB6hbr1IhfXpLnmmLFIzN0JRfjc2DA2Qbmm9NpY";
	$access_token_secret = "qnlVQklF4mD7a6Y4yg4aNnBQhvcwMZ0fWVy9NsnIB0";
	
	//OAuthオブジェクト作成
	$to = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);
	
	//メッセージ設定
	//朝8:00にどの音ゲーが2クレかをつぶやく。
	
	//今日の月日を取得
	$month = date('n')."月";
	$day = date('j')."日";
	$dayoftheweek = date('D');
	$hour = date('G');
	
	//Yahoo!のRSSから鳥取県（東部）の天気を取得
	$url = "http://rss.weather.yahoo.co.jp/rss/days/6910.xml";
	$xml = simplexml_load_file($url);
	
	//今日の天気を取得
	foreach ($xml->channel->item as $item) {
		if (strstr($item->title, $day)) {
			$description = $item->description;
		}
	}
	

	
	//曜日を判定し、ツイートメッセージを作成
	switch($dayoftheweek) {
		case "Mon":
			$dayoftheweekjap = "（月）";
			$daily_message = "「ぽくぽく」「ぎたー」「たいこ」が100円2クレ";
			break;
		case "Tue":
			$dayoftheweekjap = "（火）";
			$daily_message = "「びーまに」がパセリ料金40%ダウン";
			break;
		case "Wed":
			$dayoftheweekjap = "（水）";
			$daily_message = "「ゆびーと」がパセリ料金40%ダウン";
			break;
		case "Thu":
			$dayoftheweekjap = "（木）";
			$daily_message = "「りふれくびーと」がパセリ料金40%ダウン";
			break;
		case "Fri":
			$dayoftheweekjap = "（金）";
			$daily_message = "「ぱかぱか」がパセリ料金40%ダウン";
			break;
		case "Sat":
			$dayoftheweekjap = "（土）";
			$daily_message = "通常営業日";
			break;
		case "Sun":
			$dayoftheweekjap = "（日）";
			$daily_message = "通常営業日";
			break;
	}
	
	//雪の日は特別なメッセージを作成
	if (preg_match("/^(雪|暴風雪) .*/", $description)) {
		$daily_message = "雪の日イベント実施日です。ヽ(∀ﾟ )人(ﾟ∀ﾟ)人( ﾟ∀)ノ 音ゲー全部100円2クレ";
	}
	
	//ツイートするメッセージの作成
	$message = "【".$month.$day.$dayoftheweekjap."】 "."本日は".$daily_message."です。ゆっくりしていってね！";
	
	//ツイート（8時に1回）
	if($hour == '8') {
		$to->OAuthRequest("https://twitter.com/statuses/update.xml","POST",array("status"=>"$message"));
	}
?>
