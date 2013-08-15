<?php

  ini_set("memory_limit","1000M");

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
  *  Method  getConfig()    { Tested }
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
   
   
   /*
   *  Method  encodeConfigTemplate  { Tested }
   *   @Purpose  Determines the URL format to use based on a record's config settings 
   *    @param  array  Represents what is stored in the config settings (from db) 
   *    
   *    @return string The URI which will be loaded later on.           
   *
   */      
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
  *  Method  teamWorkFetchWebsite()  { Tested }
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
  
  
  
  /*  Method  teamWorkLoadWebsite()  { Tested } 
  *    @Purpose  Responsible for loading the website.
  *    @NOTE:    Each $internal_call (where TRUE) should be re-using the singleton instance of $this->teamExplorer['html'] ..   
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
  
  
  /*
  *  Method determineSection()  { Prototyped }
  *   @Purpose  This value does change (usually either 3 || 4), so let it be determined dynamically..  
  */ 
  public function determineSection($position, $stored_arr){ 
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
    return count( $html->getElementById($position)->children() ) -1;
   }
  }
   
  
  /*
  *  Method getChildren()   { Prototyped }
  *   @Purpose Responsible for fetching the number of children belonging to a specific node  
  *    @depends Method teamWorkLoadWebsite() 
  *    @param string $position { $center, etc.. } 
  *    @param int    $section  { ie 3 }  
  *    @param array  $stored_arr  Used as a call to internal method to check for html object's presence  
  *    
  *    @return int  Number (count) of children belonging to that node       
  */
  public function getChildren($position, $section, $stored_arr){ 
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
    return count($html->getElementById($position)->childNodes($section)->children());
   }
  }
  
  
  //@return string
  public function getDescription($position, $section, $stored_arr, $i){
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
    return $html->getElementById($position)->childNodes($section)->childNodes($i)->childNodes(2)->childNodes(0)->childNodes(0)->plaintext;
   }
  }
  
  
  //@return string
  public function getPrimePrice($position, $section, $stored_arr, $i){
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
   
    try{
     if( ($prime = $html->getElementById($position)->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(0)->childNodes(0)->childNodes(1)->plaintext) !== FALSE )
     {
      $prime = preg_replace('/\s/','',$prime);
      if($prime != '')
      {
       return $prime;
      }
      else{
       throw new Exception('Prime found, strlen is zero');        
      }
     }else{
      throw new Exception('No prime found.');
     }
    }catch(Exception $e){
     return $e->getMessage();
    }
    
   }
  } //end getPrimePrice()..
  
  
  //@return array
  public function getNewUsedSecondaryPrice($position, $section, $stored_arr, $i){
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
  
    $basket_arr = array();
    if( count( $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->children() ) < 5 )  //Signifies we only have 'new' OR 'used' prices available to us..
    {
     $href = $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(3)->childNodes(0)->href;
     if( strpos($href, 'new') != FALSE )
     {
      $basket_arr[$i]['sec_new']  = $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(3)->childNodes(0)->childNodes(0)->plaintext;
      $basket_arr[$i]['sec_used'] = 'Used node was not present.';
     }
     elseif( strpos($href, 'new') == FALSE )
     {
      $basket_arr[$i]['sec_new']  = $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(3)->childNodes(0)->childNodes(0)->plaintext;  //These are both holding the same value according to the amazon site..
      $basket_arr[$i]['sec_used'] = $basket_arr[$i]['sec_new'];                                                                                                                //""..
     }
     return $basket_arr;
    }
    elseif( count( $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->children() ) == 5 )
    {
     $basket_arr[$i]['sec_new']  =  $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(3)->childNodes(0)->childNodes(0)->plaintext;
     $basket_arr[$i]['sec_used'] =  $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(4)->childNodes(0)->childNodes(0)->plaintext;
     return $basket_arr;
    }
    elseif( count( $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->children() ) == 6 )
    {
     $basket_arr[$i]['sec_new']  =  $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(4)->childNodes(0)->childNodes(0)->plaintext;
     $basket_arr[$i]['sec_used'] =  $html->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(5)->childNodes(0)->childNodes(0)->plaintext;
     return $basket_arr;
    }
    
   }
  } //end getNewUsedSecondaryPrice()..
  
   
  } //end Crawler class..
  
  
  //Public api code..
  
  $crawler_A = new Crawler();
   $position = "center";  
  $container = array();
  $query = "
             SELECT
              product,
              type
             FROM scraper.config
             WHERE config_id = 2
   ";
  
   try{    //Produce logic relevant to querying, encoding, fetching, and loading our website..
    
    if( count($rs_arr = $crawler_A->getConfig($query)) < 2 )
    {
     throw new Exception('getConfig() did not return both product and type elements');
    }
    
    if( strlen($container['url_encoded_str'] = $crawler_A->encodeConfigTemplate($rs_arr)) < 1 )
    {
     throw new Exception('encodeConfigTemplate() failed to product a valid string.');
    }
    
    if( strlen($container['page_contents_str'] = $crawler_A->teamWorkFetchWebsite( $container )) < 1 )
    {
     throw new Exception('teamWorkFetchWebsite() failed to product a valid string.');
    }
    
    if( strlen($container['loadedPotato'] = $crawler_A->teamWorkLoadWebsite( $container['page_contents_str'], FALSE )) !== 1 )
    {
     throw new Exception('teamWorkLoadWebsite() failed to produce a loaded website (loaded potato).');
    }
    
   }catch(Exception $e){
    echo $e->getMessage();
   }
   
   $section = $crawler_A->determineSection($position, $container);
   $count_children = $crawler_A->getChildren($position, $section, $container);
    $i = 1; //Debugging: possible values are (0, 1, 2)..
   $descrip_str    = $crawler_A->getDescription($position, $section, $container, $i);
   $prime_str      = $crawler_A->getPrimePrice($position, $section, $container, $i); 
   
   try{
    if( ($new_used_sec_arr = $crawler_A->getNewUsedSecondaryPrice($position, $section, $container, $i)) !== FALSE )
    {
     if( count($new_used_sec_arr[$i]) == 1)
     {
      throw new Exception('getNewUsedSecondaryPrice() only returned one secondary price. Expecting two secondary prices. ');
     }
     elseif( count($new_used_sec_arr[$i]) == 0 )
     {
      throw new Exception('getNewUsedSecondaryPrice() did not return any secondary prices. Expecting two secondary prices. ');
     }
    }
    else{
     throw new Exception('getNewUsedSecondaryPrice() did not return a valid array');
    }
   }catch(Exception $e){
    echo $e->getMessage();
   }
   
   //Output:
   echo $descrip_str . '</br>';
   echo $prime_str . '</br>';
   print_r( $new_used_sec_arr = $crawler_A->getNewUsedSecondaryPrice($position, $section, $container, $i));
    
  
?>
