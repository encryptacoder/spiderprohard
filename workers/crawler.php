<?php
  require_once('C:/Program Files/wamp/www/spiderprohard/includes/db_conn_mysqli.php');
  require_once('../simple_html_dom.php');
  require_once('../Snoopy.class.php');
  
  class Crawler
  {
   public $teamExplorer = array(
                           'html'     => null,
                           'snoopy_A' => null
   );
   
   public $_mysqli = null;
   
   public function __construct(){
    $mysqli_conn = new dbConnectionMySQLi();
    $this->_mysqli = $mysqli_conn->_mysqli;
   }
   
   
   /*
  *  Method  getConfig()
  *   @Purpose  Fetches the configuration settings from the db.
  *    @param    string  $query  Represents the SQL query + (mySQLi syntax) to be used.
  *  
  *    @return   array   $row    The data passed back & stuffed into an array. 
  */
   public function getConfig($query){ 
    if( ($rs = $this->_mysqli->query($query)) !== FALSE)
    {
     if( mysqli_num_rows($rs) == 1 )
     {
      $row = mysqli_fetch_array($rs, MYSQLI_ASSOC);
      $this->_mysqli->close();
      return $row;
     }
    }
   } //end getConfig()..
   
   
   public function encodeConfigTemplate( $rs_config_arr ){
    $product = $rs_config_arr['product'];
    foreach($rs_config_arr as $k1 => $v1){
     if($k1 == 'type')
     {
      if($v1 == 'single')
      {
       return 'http://www.amazon.com/s/ref=nb_sb_noss_1?url=search-alias%3Daps&field-keywords=' .$product. '&sprefix=' .$product. '%2Caps&rh=i%3Aaps%2Ck%3A' .$product. '';
      }
      elseif($v1 == 'withspace')
      {
       return 'http://www.amazon.com/s/ref=nb_sb_noss_1?url=search-alias%3Daps&field-keywords=' .urlencode($product). '&rh=i%3Aaps%2Ck%3A' .urlencode($product). '';
      }
     }
    }
   } //end encodeConfigTemplate()..
   
   
   /*
  *  Method  teamWorkFetchWebsite()
  *   @Purpose  Is used by the 'simple_html_dom' instance to load itself up. 
  *    @param  array   $config_arr      Represents the config array passed back from the db.  
  *   
  *    @return string  (page contents)  The stringified website contents passed back from the 'results()' method.  
  */    
  public function teamWorkFetchWebsite( $stored_arr ){
   $this->teamExplorer['html']            = new simple_html_dom();
   $this->teamExplorer['snoopy_A']        = new Snoopy();
   $this->teamExplorer['snoopy_A']->agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
   
   if( $this->teamExplorer['snoopy_A']->fetch( $stored_arr['url_encoded_str'] ) )
   {
    return $this->teamExplorer['snoopy_A']->results;   
   }
 
  } //end teamWorkFetchWebsite()..
  
  
  
  /*  Method  teamWorkLoadWebsite()  
  *    @Purpose  Responsible for loading the website.
  *     @param  string  $string_to_load  The website (in the form of a string) to load.
  *     @param   
  *     
  *     @return int     1 for successfully loading, or 0 for fail to load.
  */    
  public function teamWorkLoadWebsite( $string_to_load, $internal_call = FALSE ){
   $html = ( !is_null($this->teamExplorer['html']) ) ? $this->teamExplorer['html'] : new simple_html_dom(); 
   
   if( $internal_call == FALSE )
   {
    $loadedPotato = ( $html->load( $string_to_load ) ) ? 1 : 0;
    return $loadedPotato;
   }
   elseif( $internal_call == TRUE )       //Used by internal API methods..
   {
    $html->load( $string_to_load );
    return $html;
   }
  
  } //end teamWorkLoadWebsite()..
  
   
  } //end Crawler class..
  
  
  //Public api code..
  /*
  $crawler_A = new Crawler();
  $container = array();
  $query = "
             SELECT
              product,
              type
             FROM scraper.config
             WHERE config_id = 1
   ";
  
   try{
    $rs_arr = $crawler_A->getConfig($query);
   }catch(Exception $e){
    echo $e->getMessage();
   }
   
   try{
     $container['url_encoded_str'] = $crawler_A->encodeConfigTemplate($rs_arr); 
   }catch(Exception $e){
    echo $e->getMessage();
   }
   
  
   try{
    $container['page_contents_str'] = $crawler_A->teamWorkFetchWebsite( $container );
   }catch(Exception $e){
    echo $e->getMessage();
   }
   
   
   try{
    $container['loadedPotato'] = $crawler_A->teamWorkLoadWebsite( $container['page_contents_str'], FALSE );
   }catch(Exception $e){
    echo $e->getMessage();
   }
   */ 
  
?>
