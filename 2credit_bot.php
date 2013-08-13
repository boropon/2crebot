<?php
	//引数に時間を渡すと、その時間の天気を取得する関数
	//Yahoo!のRSSから鳥取県（東部）の天気を取得
	function getweather($time) {
		$url = "http://rss.weather.yahoo.co.jp/rss/days/6910.xml";
		$xml = simplexml_load_file($url);
		foreach ($xml->channel->item as $item) {
			if (strstr($item->title, " ".date('j', $time)."日")) {
				$description = $item->description;
			}
		}
		$separate_description = explode(" ", $description);
		$tomorrow_weather = $separate_description[0];
		return $tomorrow_weather;
	}
	
	//引数に渡した文字列をツイート
	function tweetmessage($message) {
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

<?php
	//天気を保存しておくファイル
	$weather_file = "weather.txt";
	
	$hour = date('G');
	//朝9時に明日の天気を取得し、ファイルに保存
	if($hour == '9') {
		//明日の天気を取得
		$tomorrow_time = strtotime("tomorrow");
		$tomorrow_weather = getweather($tomorrow_time);
		
		//天気をファイル(weather.txt)に保存
		$file = fopen($weather_file, "w+");
		fwrite($file, $tomorrow_weather);
	}
	
	//朝8時に今日の音ゲー情報をツイート
	if($hour == '8') {
		//今日の曜日を確認し、曜日に対応するツイートメッセージを設定
		$dayoftheweek = date('D');
		switch($dayoftheweek) {
			case "Mon":
				$dayoftheweekjap = "月";
				$daily_message = "「ぽくぽく」「ぎたー」「たいこ」が100円2クレです。";
				break;
			case "Tue":
				$dayoftheweekjap = "火";
				$daily_message = "「びーまに」がパセリ料金40%ダウンです。";
				break;
			case "Wed":
				$dayoftheweekjap = "水";
				$daily_message = "「ゆびーと」がパセリ料金40%ダウンです。";
				break;
			case "Thu":
				$dayoftheweekjap = "木";
				$daily_message = "「りふれくびーと」がパセリ料金40%ダウンです。";
				break;
			case "Fri":
				$dayoftheweekjap = "金";
				$daily_message = "「ぱかぱか」がパセリ料金40%ダウンです。";
				break;
			case "Sat":
				$dayoftheweekjap = "土";
				$daily_message = "通常営業日です。";
				break;
			case "Sun":
				$dayoftheweekjap = "日";
				$daily_message = "通常営業日です。";
				break;
		}
		
		//前日9時に保存した天気を参照し、雪なら雪の日イベント用メッセージに変更
		$today_weather = file_get_contents($weather_file);
		if ($today_weather === "雪" || $today_weather === "暴風雪" || preg_match("/^(雪時々).*/", $today_weather)) {
			$daily_message = "雪の日イベント実施日で、音ゲー全部100円2クレです。";
		} else if (strstr($today_weather, "雪")) {
			$daily_message .= "もしかすると雪の日イベント実施日で、音ゲー全部100円2クレかもしれません。";
		}
		
		//ツイートするメッセージの作成（【[今日の月日]（[曜日]） [天気]】おはようございます☆彡 本日は[音ゲー情報]です。ゆっくりしていってね！[顔文字]）
		$today = date('j');
		$today_month = date('n');
		$face = array("（ ´Д｀）ｙ━─┛~~", "⊂(ﾟДﾟ⊂⌒｀つ≡≡≡", "（・∀・）", "( ◕ ‿‿ ◕ ) ", "ξ・`∀・)ξ", "(σ´∀`)σ", "(*´･ω･`)ﾉ ", "(｀・ω・´) ゞ", "|・ω・`）", "＼(●)／", "ξ(✿＞◡❛)ξ▄︻");
		$today_face = $face[mt_rand(0, count($face)-1)];
		$message = "【".$today_month."月".$today."日（".$dayoftheweekjap."） ".$today_weather."】 おはようございます☆彡 本日は".$daily_message."ゆっくりしていってね！".$today_face;
		//ツイート
		tweetmessage($message);
	}
?>
