<?php
/* Clase para la interacción con facebook
 * @version   0.01
 * @since     2014-11-13 (YY-MM-DD) 
 * @author    Raylin Aquino <raylin@adventures.do>
 */
class FacebookFeed {
	private $facebook,$data, $limit, $user; 
	
	function __construct($arr = array()){
		
		
		$this->facebook = new Facebook(array(
            'appId' => $arr["facebook_app_id"],
            'secret' => $arr["facebook_app_secret"]
        ));
		
		$this->limit = $arr["limit"];
		
		$this->user = $arr["user"];
        
        try {
            $this->data = $this->facebook->api("clustersoftrd/posts/", "GET", array('limit' => $this->limit));
        } catch (FacebookApiException $e) {
            error_log($e);
            $this->data = null;
        }
		
		return $this->data;
		
	}
	
    public function hash_it($text) {
        $to = array("á","é", "ñ","Ñ");
        $from = array("a","e", "n","N");
        $text = str_replace($to, $from, $text);  
        $text= preg_replace("%@(\w+)%", '<a class="special-underline trans-2"  href="https://www.facebook.com/hashtag/$1" target="_blank">@$1</a>', $text); 
        $text= preg_replace("%#(\w+)%", '<a class="special-underline trans-2"  href="https://www.facebook.com/hashtag/'.utf8_decode("$1").'" target="_blank">#$1</a>',$text); 
        return $text;
    }
    public function link_it($text) {

        $text= preg_replace(
         array(
           '/(^|\s|>)(www.[^<> \n\r]+)/iex',
           '/(^|\s|>)([_A-Za-z0-9-]+(\\.[A-Za-z]{2,3})?\\.[A-Za-z]{2,4}\\/[^<> \n\r]+)/iex',
           '/(?(?=<a[^>]*>.+<\/a>)(?:<a[^>]*>.+<\/a>)|([^="\']?)((?:https?):\/\/([^<> \n\r]+)))/iex'
         ),  
         array(
           "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\"  class=\"special-underline trans-2\" target=\"_blank\">\\2</a>&nbsp;\\3':'\\0'))",
           "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\" class=\"special-underline trans-2\"  target=\"_blank\">\\2</a>&nbsp;\\4':'\\0'))",
           "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\" class=\"special-underline trans-2\" target=\"_blank\">\\3</a>&nbsp;':'\\0'))",
         ),  
         $text
        );
        return($text);
    }	
	public function get_feeds(){
		if($this->data != null){
			return $this->data["data"];
		}
		return false;
	}

} 
?>