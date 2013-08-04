<?php
  /*
  *  //Storyline:
  *    1.) grab specific products from the database via config id..   done. jdr..
  *    2.) Urlencode the product with the url template to use         done. jdr..
  *    3.) Create a new simple_html_dom and Snoopy objects ..borrow 'teamWorkFetchWebsite()' @mortar.php        done.jdr..
  *     4.) Create a list of parameters relevant to each found item in the dom ie ( $found_descrip_parent )     NEED..
  *    5.) Create different relevant methods which will return the description, image, price, sub-price etc..   NEED to finish..
  *      
  *            
  *
  *
  *
  */
  
          
  require_once('../simple_html_dom.php');
  require_once('../Snoopy.class.php');
  
  
  //Prototype amazon scrape..
   $product_arr = array(
                    0 => array(
                           'url'     => "http://www.amazon.com/s/ref=nb_sb_noss_1?url=search-alias%3Daps&field-keywords=laptop&sprefix=laptop%2Caps&rh=i%3Aaps%2Ck%3Alaptop",
                           'product' => "laptop"
                    ),
                    
                    1 => array(
                           'url'     => "http://www.amazon.com/s/ref=nb_sb_noss_1?url=search-alias%3Daps&field-keywords=laptop+case&rh=i%3Aaps%2Ck%3Alaptop+case",
                           'product' => "laptop case"
                    )
   );
   
   $url = $product_arr[0]['url'];
  $a = new simple_html_dom();
  $b = new Snoopy();
  $b->agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
  $b->fetch( $url ); 
    $a->load( $b->results );
    
     //Works on images..
    /*
    $basket_arr = array();
    $temp_arr = $a->find( 'img' );                
    foreach($temp_arr as $key1 => $val1){
     if( $val1->src )    
     {                                
      echo $val1->src;
     }
    }
    */
    
    
    //Works for the production description..
    /* 
    $temp_arr = $a->find('span[class=lrg bold]');
    for( $i=0; $i<count($temp_arr); ++$i )
    {
     echo $temp_arr[$i]->plaintext . '</br>';
    }
    */
    
    
    
    /*
    //Works for the Big Price found to the right of the image..
    $temp_arr = $a->find('span[class=bld lrg red]');
    for( $i=0; $i<count($temp_arr); ++$i )
    {
     echo $temp_arr[$i]->plaintext . '</br>';
    }
    */
    
    
    /*
    //Works for the 'more buying choices'... 
    $temp_arr = $a->find('li[class=med grey mkp2]');
    for( $i=0; $i<count($temp_arr); ++$i )
    {
     echo $temp_arr[$i]->plaintext . '</br>';
    }
    */
    
    
    //PROTOTYPING some more using the DOM..
     $section = 3;
     $basket_arr = array();
     $count_children = $a->getElementById("center")->childNodes($section)->children();    //First get the number of children to a specific node..
     for( $i=0; $i<count($count_children); ++$i )
     {
      try{
       //Grab the description..
      $descrip = $a->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(2)->childNodes(0)->childNodes(0)->plaintext;
       $basket_arr[$i]['description'] = $descrip;
      }catch(Exception $e){
       return false;
      }
      
      try{
       //Grab the prime price..
       $prime = $a->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(0)->childNodes(0)->childNodes(1)->plaintext;
        $basket_arr[$i]['prime'] = $prime;
      }catch(Exception $e){
       return false;
      }
      
      try{
       //Grab the secondary NEW price..
       $sec_new = $a->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(3)->childNodes(0)->childNodes(0)->plaintext;
        $basket_arr[$i]['sec_new'] = $sec_new;
      }catch(Exception $e){
       return false; 
      }
      
       
      try{
       //Grab the USED price..
       if( count( $a->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->children() ) < 5 )  //Signifies we only have only 1/2 secondaries available to us..
       {
        $basket_arr[$i]['sec_used'] = "";
       }else{
        $sec_used = $a->getElementById("center")->childNodes($section)->childNodes($i)->childNodes(3)->childNodes(4)->childNodes(0)->childNodes(0)->plaintext;
        $basket_arr[$i]['sec_used'] = $sec_used;
       }
           
      }catch(Exception $e){
       return false;
      }
     
     } //end for..
     //print_r($basket_arr);
     
     
     //Aethetics only..
     foreach($basket_arr as $k1 => $v1){
      echo '</br>';
      foreach($v1 as $k2 => $v2){
       echo $k2 . ': ' .$v2. '</br>';
      }
     }
     
   
   
   
  
?>
