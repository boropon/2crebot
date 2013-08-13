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
	
	//ブログの更新情報をチェックする関数
	//更新があればブログの情報を取得、更新がなければ空文字列を返却
	function getbloginfo() {
		
		$blog_recordfile = "blog_record.txt";
		$blog_latest_published = file_get_contents($blog_recordfile);
		
		$url = "http://www.3rdplanet.jp/shop/tottori/atom.xml";
		$xml = simplexml_load_file($url);
		
		$blog_information = "";
		if (strcmp($xml->entry->published, $blog_latest_published) != 0) {
			$blog_information = $xml->entry->title." ".$xml->entry->link->attributes()->href;
			
			$file = fopen($blog_recordfile, "w+");
			fwrite($file, $xml->entry->published);
		}
		return $blog_information;
	}
	
	//顔文字をランダムに取得する
	function getface() {
		$face = array("（ ´Д｀）ｙ━─┛~~", "⊂(ﾟДﾟ⊂⌒｀つ≡≡≡", "（・∀・）", "( ◕ ‿‿ ◕ ) ", "ξ・`∀・)ξ", "(σ´∀`)σ", "(*´･ω･`)ﾉ ", "(｀・ω・´) ゞ", "|・ω・`）", "＼(●)／", "ξ(✿＞◡❛)ξ▄︻");
		return $face[mt_rand(0, count($face)-1)];
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
	
	$hour = date('G');
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
				$daily_message = "「ふみふみ」「りふれくびーと」がパセリ料金40%ダウンです。";
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
		
		//今日の天気を取得
		$now_time = strtotime("now");
		$today_weather = getweather($now_time);
		
		//ツイートするメッセージの作成（【[今日の月日]（[曜日]） [天気]】おはようございます☆彡 本日は[音ゲー情報]です。ゆっくりしていってね！[顔文字]）
		$today = date('j');
		$today_month = date('n');
		$today_face = getface();
		$message = "【".$today_month."月".$today."日（".$dayoftheweekjap."） ".$today_weather."】 おはようございます☆彡 本日は".$daily_message."ゆっくりしていってね！".$today_face;
		
		//ツイート
		tweetmessage($message);
	}
	
	//1時間毎にサープラ鳥取店の更新情報を確認、更新があればツイート
	$blog_info = getbloginfo();
	if(strcmp($blog_info, "") != 0) {
		$face = getface();
		$message = "ブログの更新がありました☆彡 ".$face."　⇒　".$blog_info;
		print $message;
		tweetmessage($message);
	}
?>
