<?php
	$weather_file = "weather.txt";
	
	$hour = date('G');
	//朝9時に明日の天気を取得し、ファイルに保存
	if($hour == '9') {
		//明日の天気を取得
		//Yahoo!のRSSから鳥取県（東部）の天気を取得
		$url = "http://rss.weather.yahoo.co.jp/rss/days/6910.xml";
		$xml = simplexml_load_file($url);
		$tomorrow = date('j', strtotime("tomorrow"));
		foreach ($xml->channel->item as $item) {
			if (strstr($item->title, " ".$tomorrow."日")) {
				$description = $item->description;
			}
		}
		$separate_description = explode(" ", $description);
		$tomorrow_weather = $separate_description[0];
		
		//天気をファイル(weather.txt)に保存
		$file = fopen($weather_file, "w+");
		fwrite($file, $tomorrow_weather);
	}
	
	//朝8時に今日の音ゲー情報をツイート
	if($hour == '8') {
		//曜日に対応するツイートメッセージを設定
		$dayoftheweek = date('D');
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
		
		//前日9時に保存した天気を参照し、雪なら雪の日イベントメッセージに上書き
		$today_weather = file_get_contents($weather_file);
		if (strstr($today_weather, "雪")) {
			$daily_message = "雪の日イベント実施日です。ヽ(∀ﾟ )人(ﾟ∀ﾟ)人( ﾟ∀)ノ 音ゲー全部100円2クレ";
		}
		
		//ツイートするメッセージの作成
		$today = date('j');
		$today_month = date('n');
		$message = "【".$today_month."月".$today."日".$dayoftheweekjap."】 "."本日は".$daily_message."です。ゆっくりしていってね！";
		
		//ツイートするための準備
		require_once("twitteroauth.php");
		$consumer_key = "epZiUS66NYxESrtIy1Cg";
		$consumer_secret = "NhfD58UCb6QdE0aCV8PSIhcsGjcJNKuIX6zf3RPRfSI";
		$access_token = "452778967-FBB6hbr1IhfXpLnmmLFIzN0JRfjc2DA2Qbmm9NpY";
		$access_token_secret = "qnlVQklF4mD7a6Y4yg4aNnBQhvcwMZ0fWVy9NsnIB0";
		$to = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);
		//ツイート
		$to->OAuthRequest("https://twitter.com/statuses/update.xml","POST",array("status"=>"$message"));
	}
?>
