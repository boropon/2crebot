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
	//1時間毎にサープラ鳥取店の更新情報を確認、更新があればツイート
	$blog_info = getbloginfo();
	if(strcmp($blog_info, "") != 0) {
		$face = getface();
		$message = "ブログの更新がありました☆彡 ".$face."　⇒　".$blog_info;
		print $message;
		tweetmessage($message);
	}
?>
