<?php

class WPLA_AmazonWebHelper {

    var $errors      = array();
    var $success     = false;
    var $images      = array();
    var $description = false;
    var $dblogger;
	
	public function __construct() {
		// global $wpla_logger;
		// $this->logger = &$wpla_logger;
	}


    // getter methods
    public function getImages() {
        return $this->images;
    } 
    public function getDescription() {
        return $this->description;
    } 


    public function loadListingDetails( $listing_id ) {

        // load page content
        $listing_url  = $this->getListingURL( $listing_id );
        $html_content = $this->fetchPageContent( $listing_url );
        if ( empty($html_content) ) return false;

        // process content
        $this->processListingDescription( $html_content );
        $this->processListingImages( $html_content );

        return true;
    }


    public function processListingDescription( $html_content ) {

        // check for inline description
        $this->description = '';
        if ( preg_match("/(<div class=\"productDescriptionWrapper\">)(.*)(<div class=\"emptyClear\">)/usm", $html_content, $matches ) ) {
            // echo "<pre>MATCH1: ";print_r($matches);echo"</pre>";die();
            $this->description = $matches[2];
        }


        // check for iframeContent
        if ( empty( $this->description ) ) {

            if ( preg_match("/(var iframeContent = \")(.*)(\")/uUsm", $html_content, $matches ) ) {
                // echo "<pre>MATCH1: ";print_r($matches);echo"</pre>";#die();
                $this->description = $matches[2];

                // decode iframeContent and use as html_content
                $html_content = urldecode( $this->description );

                // check for inline description - in converted iframeContent
                if ( preg_match("/(<div class=\"productDescriptionWrapper\">)(.*)(<div class=\"emptyClear\">)/usm", $html_content, $matches ) ) {
                    // echo "<pre>MATCH2: ";print_r($matches);echo"</pre>";die();
                    $this->description = $matches[2];
                }

            }
        }

        // trim leading and trailing spaces
        $this->description = trim( $this->description );

        // if ( empty( $desc ) ) {
        //     $error_msg  = sprintf( __('There was a problem fetching product details for %s.','wpla'), $item['asin'] );
        //     $error_msg  .= ' The product description received from Amazon was empty.';
        //     WPLA()->logger->error( $error_msg );
        //     $this->errors[] = $error_msg;
        //     $this->success = false;
        // }
        // echo "<pre>";print_r(htmlspecialchars($this->description));echo"</pre>";#die();
        WPLA()->logger->info('listing description: '.strlen($this->description).' bytes');

    } // processListingDescription()


    public function processListingImages( $html_content ) {

        // extract additional images
        $product_images = array();
        if ( preg_match("/('colorImages': { 'initial': )(.*}])}/uUsm", $html_content, $matches ) ) {
            // WPLA()->logger->info('MATCHED JSON: '.print_r($matches[2],1));
            $json = $matches[2];
            $image_data = json_decode($json);
            if ( is_array($image_data) ) {
                foreach ( $image_data as $img ) {
                    if ( $img->hiRes ) {
                        $product_images[] = $img->hiRes;
                    } elseif ( $img->large ) {
                        $product_images[] = $img->large;
                    }
                }
            }
            // echo "<pre>";print_r($product_images);echo"</pre>";die();
        }

        // // extract variation images (not implemented yet - for now, each variation image is fetched separately)
        // //  data["colorImages"] = {"Red":[{"large":"http://ecx.images-amazon.com/images/I/41qA4W--dlL.jpg","variant":"MAIN","hiRes":"http://ecx.images-amazon.com/images/I/61YLNgtXY%2BL._UL1100_.jpg","thumb":"http...
        // $variation_images = array();
        // if ( preg_match('/(data\["colorImages"\] = )(.*);/uUsm', $html_content, $matches ) ) {
        //     // echo "<pre>MATCH: ";print_r($matches);echo"</pre>";#die();
        //     $json = $matches[2];
        //     $image_data = json_decode($json);
        //     echo "<pre>JSON: ";print_r($image_data);echo"</pre>";#die();
        //     if ( is_array($image_data) ) {
        //         foreach ( $image_data as $img ) {
        //             if ( $img->hiRes ) {
        //                 $variation_images[] = $img->hiRes;
        //             } elseif ( $img->large ) {
        //                 $variation_images[] = $img->large;
        //             }
        //         }
        //     }
        //     echo "<pre>";print_r($variation_images);echo"</pre>";die();
        // }

        $this->images = $product_images;
        WPLA()->logger->info('found '.sizeof($this->images).' listing images');
        // WPLA()->logger->info('found '.sizeof($this->images).' listing image: '.print_r($this->images,1));

    } // processListingImages()

    public function getListingURL( $listing_id ) {

        $lm      = new WPLA_ListingsModel();
        $item    = $lm->getItem( $listing_id );

        // build listing URL
        $listing_url = 'http://www.amazon.com/dp/'.$item['asin'].'/';
        if ( $item['account_id'] ) {
            $account = new WPLA_AmazonAccount( $item['account_id'] );
            $market  = new WPLA_AmazonMarket( $account->market_id );
            $listing_url = 'http://www.'.$market->url.'/dp/'.$item['asin'].'/';
        }

        return $listing_url;
    } // getListingURL()


    public function fetchPageContent( $listing_url ) {
        WPLA()->logger->info('fetching URL: '.$listing_url);

        // fetch HTML content
        $response = wp_remote_get( $listing_url, array( 
            'timeout'    => 15,
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36',
                            // without a known user-agent, Amazon will return different content which is missing the require JS code to extract variation images
        ));
        // WPLA()->logger->info("BODY: ".$response['body']);

        // handle errors
        if ( is_wp_error( $response ) ) {
            // echo "<pre>";print_r($response);echo"</pre>";   
            // $this->showMessage( "Couldn't fetch URL $listing_url - ".$response->get_error_message(), 1, 1 );
            WPLA()->logger->error("Couldn't fetch URL $listing_url - ".$response->get_error_message());
            wpla_show_message("Couldn't fetch URL $listing_url - ".$response->get_error_message() );
            $this->errors[] = "Couldn't fetch URL $listing_url - ".$response->get_error_message(); // doesn't show
            return false;
        }
        if ( $response['response']['code'] != 200 ) {
            // echo "<pre>Couldn't fetch URL $listing_url - server returned error code ".$response['response']['code']."</pre>";
            // $this->showMessage( "Couldn't fetch URL $listing_url - server returned error code ".$response['response']['code'], 1, 1 );
            WPLA()->logger->error("Couldn't fetch URL $listing_url - server returned error code ".$response['response']['code'] );
            wpla_show_message("Couldn't fetch URL $listing_url - server returned error code ".$response['response']['code'] );
            $this->errors[] = "Couldn't fetch URL $listing_url - server returned error code ".$response['response']['code'];
            return false;
        }


        // log to db - enable only for debugging (limited to 64k)
        // $this->dblogger = new WPLA_AmazonLogger();
        // $this->dblogger->updateLog( array(
        //     'callname'    => 'wp_remote_get',
        //     'request'     => 'internal action hook',
        //     'parameters'  => maybe_serialize( $listing_url ),
        //     'request_url' => $listing_url,
        //     'account_id'  => '',
        //     'market_id'   => '',
        //     'response'    => $response['body'],
        //     'result'      => json_encode( $response ),
        //     'success'     => 'Success'
        // ));


        // return HTML content
        $html_content  = $response['body'];
        $this->success = true;
        // echo "<pre>";htmlspecialchars($html_content);echo"</pre>";#die();

        return $html_content;
    } // fetchPageContent()



} // class WPLA_AmazonWebHelper
