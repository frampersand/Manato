<?php
define('BOT_TOKEN', '/***** TELEGRAM BOT TOKEN *****/');
define('MANATO_API', '/***** MANATO API ADDRESS *****/');
require("map.php");
require("telegram.php");
$telegram = new Telegram(BOT_TOKEN);
$result = $telegram->getData();

if($result["message"]["text"]{0} == '/'){
	$c = explode(' ', $result["message"]["text"]);
	switch(strtolower(trim($c[0]))) {
			
		case '/start':
			$response = "Greetings adventurer, my name is Manato. I'm a bot created for the sole purpose of assisting my master in his own roleplaying games sessions. My master is @Frampersand in case you are interested in either joining his games or using my services for your own.";
            $content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');	
	        $telegram->sendMessage($content);
			break;

		case '/help':
			$response = "Coming soon.. commands are being written right now.";
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');	
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/

		case (preg_match('/^\/(\d+)d(\d+)$/i', $c[0]) ? true : false) :
			preg_match_all('/^\/(\d+)d(\d+)$/i', $c[0], $_D);
			$response = "{$result["message"]["from"]["username"]} lanzó:\n";
			$_S = 0;
			$_P = array();
			for($i=0;$i<$_D[1][0];$i++) {
				$_T = rand(1, $_D[2][0]);
				$_S = $_S + $_T;
				$_P[] = $_T;
			}
			$response .= implode(' + ', $_P)."\n";
			$response .= "Total: {$_S}";
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');	
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/

		case '/register':
            $data = [
                "action" => "register",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
                "group_name" => $result["message"]["chat"]["title"],
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/

		case "/groupinfo":
			$data = [
                "action" => "groupinfo",
                "group_id" => $result["message"]["chat"]["id"],
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/

		case "/changename":
            $new_name = substr($result["message"]["text"], 12);
			 $data = [
                "action" => "changename",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
                "new_name" => $new_name,
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;

/**************************************************************************************************/

		case '/leave':
			 $data = [
                "action" => "leave",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/
		
		case '/map':
			$data = [
                "action" => "check_admin",
                "user_id" => $result["message"]["from"]["id"],
                "group_id" => $result["message"]["chat"]["id"],
            ];
            $params = http_build_query($data);
			$is_admin = file_get_contents(MANATO_API."?".$params);
			if($is_admin){
				if(!empty($result["message"]["chat"]["id"])){
					$map = curlmap("https://donjon.bin.sh/fantasy/dungeon/index.cgi");
					preg_match_all("/<img id=\"map_img\" src=\"(.*?)\"/i", $map, $matches);
					$img = "http://donjon.bin.sh{$matches[1][0]}";
					$fileimg = savemap($img);
					$img = curl_file_create('maps/'.$fileimg["basename"],'image/jpg');
					$content = array('chat_id' => $result["message"]["chat"]["id"], 'photo' => $img, 'caption' => $item["text"], "reply_markup" => $keyb);
					$status = $telegram->sendPhoto($content);
				}else{
					$response = "I'm sorry, this command is reserved only for groups";
					$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	       			$telegram->sendMessage($content);
				}
			}else{
				$response = "This command is reserved for the GM.";
				$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	       		$telegram->sendMessage($content);
			}
			break;

/**************************************************************************************************/

		case '/item':
			$command = json_encode($c);
			$data = [
                "action" => "item",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
				"command" => $command,	
            ];
			$params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/
			
		case '/inventory':
			if(isset($c[1]))
				$target = $c[1];
			$data = [
                "action" => "inventory",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
				"target" => $target,
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
/**************************************************************************************************/
			
		case '/initiative':
			if(isset($c[1]))
				$enemies = $c[1];
			$data = [
                "action" => "initiative",
                "user_id" => $result["message"]["from"]["id"],
                "user_name" => $result["message"]["from"]["username"],
                "group_id" => $result["message"]["chat"]["id"],
				"enemies" => $enemies,
            ];
            $params = http_build_query($data);
            $response = file_get_contents(MANATO_API."?".$params);
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
			
		default:
			$response = "My apologies, I don't understand that command.";
			$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
	        $telegram->sendMessage($content);
			break;
	}
}else{
	if(!empty($result["message"]["text"])){
		$response = "I'm sorry, my master doesn't let me talk with strangers at this moment :(";
		$content = array('chat_id' => $result["message"]["chat"]["id"], 'text' => $response, "parse_mode" => 'html');
		$telegram->sendMessage($content);
	}
}
?>
