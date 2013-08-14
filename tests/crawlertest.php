<?php
 /*
 *  Test-Driven Development (TDD)
 *   1.) Add a little test.
 *   2.) Run all tests and fail.
 *   3.) Make a little change.
 *   4.) Run the tests and succeed.
 *   5.) Refactor to remove duplication; Eliminating the duplication, thus eliminates the dependency between the test and the code.     
 *
 */
 
 /*
 *  Preface: Testing a dynamic web service utilizing DOM methods/properties
 *   consumes much time and resources.
 *   Solution: For the purposes of web crawling a dynamic site ie Amazon, 
 *    I have chosen to just test (up to) loading the actual website.
 *    
 *    Everything else: The public class has tested methods, and also prototyped (DOM related) methods.
 *    
 *    Also, the prototype interface shall use those tested methods and also those prototyped (DOM related) methods.  
 */  
 
 require_once('../simple_html_dom.php');
 require_once('../Snoopy.class.php');
 require_once('../workers/crawler.php');
 
 Class CrawlerTest extends PHPUnit_Framework_TestCase
 {
  protected $_crawler = null;
  
  //REFERENCE: See ' testGetNumberOfChildrenForElementBelongingToGetElementByIdArray ' ..
  
  protected $_section = 3;
  /* 
  *  Test seperately for children 0, 1, 2:
  *    Each child will endure a series of tests, esp for secondary price value.  
  */   
  protected $_mock_which_child = 0;  //This is the numerator for count_children (ie 0/2) where count_children is 3..
  
  public function setUp(){
   $this->_crawler = new Crawler();
  }
  
  public function tearDown(){
   unset($this->_crawler);
  }
  
  
  public function testFetchConfigurationSettingsForAParticularProductID(){
   $config_id = 2;
   $this->assertInternalType('int', $config_id);
   $query = "
             SELECT
              product,
              type
             FROM scraper.config
             WHERE config_id = $config_id
   ";
  
   $config_arr = $this->_crawler->getConfig( $query );
    $this->assertNotEmpty( $config_arr );
    $this->assertInternalType('array', $config_arr);
   
   $stored_arr = array();
   $stored_arr['config_arr'] = $config_arr;    //This is our 'result set' passed back as a converted array..
    
   return $stored_arr;
  }  
  
  
  /**
   *  @depends testFetchConfigurationSettingsForAParticularProductID 
   */
  public function testChooseTemplateURLFromConfigSettings( array $stored_arr ){
   $url_encoded_str = $this->_crawler->encodeConfigTemplate( $stored_arr['config_arr'] );
    $this->assertInternalType('string', $url_encoded_str);
    
    $stored_arr['url_encoded_str'] = $url_encoded_str;
    return $stored_arr;
  }
  
  /**
   *  @depends testChooseTemplateURLFromConfigSettings 
   */
   public function testInvokeTeamworkAndCheckThatOurFetchedWebsiteIsAString( array $stored_arr ){
   $page_contents_str = $this->_crawler->teamWorkFetchWebsite( $stored_arr );
   
   $this->assertInternalType('string', $page_contents_str);              //Assert that our page contents (being fetched via snoopy & loaded into 'html parser object' is STRING type only)...
    $stored_arr['page_contents_str'] = $page_contents_str;
   return $stored_arr;
  }
  
  
  /**
   *  @depends testInvokeTeamworkAndCheckThatOurFetchedWebsiteIsAString 
   */
  public function testInvokeTeamworkAndCheckThatOurFetchedWebsiteLoaded( array $stored_arr ){
   $internal_call = FALSE;
   $loadedPotato = $this->_crawler->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call );    
   $this->assertEquals(1, $loadedPotato ); 
   
   $stored_arr['loadedPotato'] = $loadedPotato;
   return $stored_arr;
  }
  
 }
?>
