<?php
      //The following were a part of 'crawlertest.php' :  
  
  /**
   *  @depends testInvokeTeamworkAndCheckThatOurFetchedWebsiteLoaded 
   */
  public function testApiMethodfetchProductImgSrc( array $stored_arr ){
   
   try{
    $this->assertEquals( 1, $stored_arr['loadedPotato'] );
   }catch(Exception $e){
    $this->fail('The fetched website was not properly loaded. loadedPotato is not 1.');
    return;
   }
   $img_arr = $this->_crawler->fetchProductImgSrc( $stored_arr );    // This should be the image which is sibling to the current item being iterated...
  
  }
  
  
  /**
   *  @depends testInvokeTeamworkAndCheckThatOurFetchedWebsiteLoaded 
   */
  public function testApiMethodfetchProductDescrip( array $stored_arr ){
   
   try{
    $this->assertEquals( 1, $stored_arr['loadedPotato'] );
   }catch(Exception $e){
    $this->fail('The fetched website was not properly loaded. loadedPotato is not 1.');
    return;
   }
   $img_arr = $this->_crawler->fetchProductDescrip( $stored_arr, $this->found_descrip_parent, $this->found_descrip_child );    // This should be the image which is sibling to the current item being iterated...
  
  }
  
  
  
  //The following were a part of 'crawler.php' :
  
   /*
  *  Method  fetchProductImgSrc()
  *   @NOTE:   This method differs from its sister spiderprosoft relevant method.    
  *   @Purpose Fetch the image source belonging to the respective product URI.    
  *    @param  string  $src             The URI belonging to the image we wish to fetch.
  *    @param  string  $stored_arr      The haystack (page) we wish to search from. 
  *    @param  string  $current_parent  The parent element we are crawling.
  *    
  *    @return string  The match we find in haystack, based off of needle.
  */      
  public function fetchProductImgSrc( $stored_arr ){
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
    $basket_arr = array();
  
    $temp_arr = $html->find( 'img' );                
    foreach($temp_arr as $key1 => $val1){
     if( $val1->src )    
     {                                
      return $val1->src;
     }
    }
   }
  } //end fetchImgSrc()..
  
  
  
  /*
  *  Method  fetchProductDescrip
  *   @Purpose Fetch the description from the respective description column.
  *    @param  array   $stored_arr      The array containing our haystack we wish to search from.
  *    @param  string  $current_parent  The parent element ( needle 1/2 ).    
  *    @param  string  $current_child   The parent element ( needle 2/2 ).
  *      
  *    @return string                   The match we find in haystack, based off of needle.  
  */
  public function fetchProductDescrip( $stored_arr, $found_parent, $found_child ){
   $internal_call = TRUE;
   if( $html = $this->teamWorkLoadWebsite( $stored_arr['page_contents_str'], $internal_call ) )
   {
    $basket_arr = array();
    $parentAndChild = $found_parent . '[' .$found_child . ']';   
    $weave = $html->find( $parentAndChild );
    for( $i=0; $i<count($weave); ++$i )
    {
     $basket_arr[] = $weave[$i]->plaintext;
    }
    return $basket_arr;  
   } 
  } //end fetchProductDescrip


?>
