<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    // If this file is called directly, EJECT EJECT EJECT!
    exit('First of all, how dare you!');
endif;


/**
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

class Schema_Scalpel_Activator
{
    public static function db_tables_initializer()
    {
        $schema_examples = [
            <<<BREADCRUMBS
{"@context":"https://schema.org/","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Books","item":"https://example.com/books"},{"@type":"ListItem","position":2,"name":"Science Fiction","item":"https://example.com/books/sciencefiction"},{"@type":"ListItem","position":3,"name":"Award Winners"}]}
BREADCRUMBS,
            <<<COURSE
{"@context":"https://schema.org/","@type":"Course","name":"Introduction to Computer Science and Programming","description":"Introductory CS course laying out the basics.","provider":{"@type":"Organization","name":"University of Technology - Eureka","sameAs":"http://www.ut-eureka.edu"}}
COURSE,
            <<<FAQS
{"@context":"https://schema.org/","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"What is the return policy?","acceptedAnswer":{"@type":"Answer","text":"<p>Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange. Items purchased online or in-store may be returned to any store.</p><p>Online purchases may be returned via a major parcel carrier. <a href=http://example.com/returns> Click here </a> to initiate a return.</p>"}},{"@type":"Question","name":"How long does it take to process a refund?","acceptedAnswer":{"@type":"Answer","text":"We will reimburse you for returned items in the same way you paid for them. For example, any amounts deducted from a gift card will be credited back to a gift card. For returns by mail, once we receive your return, we will process it within 4–5 business days. It may take up to 7 days after we process the return to reflect in your account, depending on your financial institution&apos;s processing time."}},{"@type":"Question","name":"What is the policy for late/non-delivery of items ordered online?","acceptedAnswer":{"@type":"Answer","text":"<p>Our local teams work diligently to make sure that your order arrives on time, within our normaldelivery hours of 9AM to 8PM in the recipient&apos;s time zone. During  busy holiday periods like Christmas, Valentine&apos;s and Mother&apos;s Day, we may extend our delivery hours before 9AM and after 8PM to ensure that all gifts are delivered on time. If for any reason your gift does not arrive on time, our dedicated Customer Service agents will do everything they can to help successfully resolve your issue.</p><p><a href=https://example.com/orders/>Click here</a> to complete the form with your order-related question(s).</p>"}},{"@type":"Question","name":"When will my credit card be charged?","acceptedAnswer":{"@type":"Answer","text":"We&apos;ll attempt to securely charge your credit card at the point of purchase online. If there&apos;s a problem, you&apos;ll be notified on the spot and prompted to use another card. Once we receive verification of sufficient funds, your payment will be completed and transferred securely to us. Your account will be charged in 24 to 48 hours."}},{"@type": "Question","name":"Will I be charged sales tax for online orders?","acceptedAnswer":{"@type": "Answer","text":"Local and State sales tax will be collected if your recipient&apos;s mailing address is in: <ul><li>Arizona</li><li>California</li><li>Colorado</li></ul>"}}]}
FAQS,
            <<<HOWTO
{"@context":"https://schema.org/","@type":"HowTo","name":"How to tile a kitchen backsplash","description":"Any kitchen can be much more vibrant with a great tile backsplash. This guide will help you install one with beautiful results, like our example kitchen seen here.","image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo.jpg","height":"406","width":"305"},"estimatedCost":{"@type":"MonetaryAmount","currency":"USD","value":"100"},"supply":[{"@type":"HowToSupply","name":"tiles"},{"@type":"HowToSupply","name":"thin-set mortar"},{"@type":"HowToSupply","name":"tile grout"},{"@type":"HowToSupply","name":"grout sealer"}],"tool":[{"@type":"HowToTool","name":"notched trowel"},{"@type":"HowToTool","name":"bucket"},{"@type":"HowToTool","name":"large sponge"}],"step":[{"@type":"HowToStep","url":"https://example.com/kitchen#step1","name":"Prepare the surfaces","itemListElement":[{"@type":"HowToDirection","text":"Turn off the power to the kitchen and then remove everything that is on the wall, such as outlet covers, switchplates, and any other item in the area that is to be tiled."},{"@type":"HowToDirection","text":"Then clean the surface thoroughly to remove any grease or other debris and tape off the area."}],"image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo-step1.jpg","height":"406","width":"305"}},{"@type":"HowToStep","name":"Plan your layout","url":"https://example.com/kitchen#step2","itemListElement":[{"@type":"HowToTip","text":"The creases created up until this point will be guiding lines for creating the four walls of your planter box."},{"@type":"HowToDirection","text":"Lift one side at a 90-degree angle, and fold it in place so that the point on the paper matches the other two points already in the center."},{"@type":"HowToDirection","text":"Repeat on the other side."}],"image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo-step2.jpg","height":"406","width":"305"}},{"@type":"HowToStep","name":"Prepare your and apply mortar (or choose adhesive tile)","url":"https://example.com/kitchen#step3","itemListElement":[{"@type":"HowToDirection","text":"Follow the instructions on your thin-set mortar to determine the right amount of water to fill in your bucket. Once done, add the powder gradually and make sure it is thoroughly mixed."},{"@type":"HowToDirection","text":"Once mixed, let it stand for a few minutes before mixing it again. This time do not add more water. Double check your thin-set mortar instructions to make sure the consistency is right."},{"@type":"HowToDirection","text":"Spread the mortar on a small section of the wall with a trowel."},{"@type":"HowToTip","text":"Thinset and other adhesives set quickly so make sure to work in a small area."},{"@type":"HowToDirection","text":"Once it&apos;s applied, comb over it with a notched trowel."}],"image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo-step3.jpg","height":"406","width":"305"}},{"@type":"HowToStep","name":"Add your tile to the wall","url":"https://example.com/kitchen#step4","itemListElement":[{"@type":"HowToDirection","text":"Place the tile sheets along the wall, making sure to add spacers so the tiles remain lined up."},{"@type":"HowToDirection","text":"Press the first piece of tile into the wall with a little twist, leaving a small (usually one-eight inch) gap at the countertop to account for expansion. use a rubber float to press the tile and ensure it sets in the adhesive."},{"@type":"HowToDirection","text":"Repeat the mortar and tiling until your wall is completely tiled, Working in small sections."}],"image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo-step4.jpg","height":"406","width":"305"}},{"@type":"HowToStep","name":"Apply the grout","url":"https://example.com/kitchen#step5","itemListElement":[{"@type":"HowToDirection","text":"Allow the thin-set mortar to set. This usually takes about 12 hours. Don&apos;t mix the grout before the mortar is set, because you don&apos;t want the grout to dry out!"},{"@type":"HowToDirection","text":"To apply, cover the area thoroughly with grout and make sure you fill all the joints by spreading it across the tiles vertically, horizontally, and diagonally. Then fill any remaining voids with grout."},{"@type":"HowToDirection","text":"Then, with a moist sponge, sponge away the excess grout and then wipe clean with a towel. For easier maintenance in the future, think about applying a grout sealer."}],"image":{"@type":"ImageObject","url":"https://example.com/photos/1x1/photo-step5.jpg","height":"406","width":"305"}}],"totalTime":"P2D"}
HOWTO,
            <<<LOCALBUSINESS
{"@context":"https://schema.org/","@type":"LocalBusiness", "additionalType":"Restaurant","image":["https://example.com/photos/1x1/photo.jpg","https://example.com/photos/4x3/photo.jpg","https://example.com/photos/16x9/photo.jpg"],"name":"Dave&apos;s Steak House","address":{"@type":"PostalAddress","streetAddress":"148 W 51st St","addressLocality":"New York","addressRegion":"NY","postalCode":"10019","addressCountry":"US"},"review":{"@type":"Review","reviewRating":{"@type":"Rating","ratingValue":"4","bestRating":"5"},"author":{"@type":"Person","name":"Lillian Ruiz"}},"geo":{"@type":"GeoCoordinates","latitude":"40.761293","longitude":"-73.982294"},"url":"http://www.example.com/restaurant-locations/manhattan","telephone":"+12122459600","servesCuisine":"American","priceRange":"$$$","openingHoursSpecification":[{"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday"],"opens":"11:30","closes":"22:00"},{"@type":"OpeningHoursSpecification","dayOfWeek":["Wednesday","Thursday","Friday"],"opens":"11:30","closes":"23:00"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Saturday","opens":"16:00","closes":"23:00"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Sunday","opens":"16:00","closes":"22:00"}],"menu":"http://www.example.com/menu","acceptsReservations":"True"}
LOCALBUSINESS,
            <<<NEWSARTICLE
{"@context":"https://schema.org/","@type":"NewsArticle","headline":"Article headline","image":["https://example.com/photos/1x1/photo.jpg","https://example.com/photos/4x3/photo.jpg","https://example.com/photos/16x9/photo.jpg"],"datePublished":"2015-02-05T08:00:00+08:00","dateModified":"2015-02-05T09:20:00+08:00"}
NEWSARTICLE,
            <<<ORGANIZATION
{"@context":"http://schema.org/","@id":"https://www.example.com/#organization","@type":"Organization","name":"Example","url":"https://www.example.com/","logo":"https://www.example.com/our-logo.jpg","subOrganization":{"@type":"Organization","name":"Example Support","url":"https://support.example.com","@id":"https://support.example.com/#organization"},"contactPoint":[{"@type":"ContactPoint","telephone":"+1-800-555-7753","contactType":"sales","areaServed":"US"},{"@type":"ContactPoint","telephone":"+1-800-555-2273","contactType":"technical support","areaServed":"US","availableLanguage":["EN","ES"]},{"@type":"ContactPoint","telephone":"+1-800-555-2273","contactType":"customer support","areaServed":"US","availableLanguage":["EN","ES"]}],"sameAs":["http://www.wikidata.org/entity/A123","https://www.youtube.com/user/Example","https://www.linkedin.com/company/example","https://www.facebook.com/Example","https://www.twitter.com/Example"]}
ORGANIZATION,
            <<<PRODUCT
{"@context":"https://schema.org/","@type":"Product","name":"Executive Anvil","image":["https://example.com/photos/1x1/photo.jpg","https://example.com/photos/4x3/photo.jpg","https://example.com/photos/16x9/photo.jpg"],"description":"Sleeker than ACME&apos;s Classic Anvil, the Executive Anvil is perfect for the business traveler looking for something to drop from a height.","sku":"0446310786","mpn":"925872","brand":{"@type":"Brand","name":"ACME"},"review":{"@type":"Review","reviewRating":{"@type":"Rating","ratingValue":"4","bestRating":"5"},"author":{"@type":"Person","name":"Fred Benson"}},"aggregateRating":{"@type":"AggregateRating","ratingValue":"4.4","reviewCount":"89"},"offers":{"@type":"Offer","url":"https://example.com/anvil","priceCurrency":"USD","price":"119.99","priceValidUntil":"2020-11-20","itemCondition":"https://schema.org/UsedCondition","availability":"https://schema.org/InStock"}}
PRODUCT,
            <<<REVIEW
{"@context":"https://schema.org/","@type":"Review","itemReviewed":{"@type":"Restaurant","image":"http://www.example.com/seafood-restaurant.jpg","name":"Legal Seafood","servesCuisine":"Seafood","priceRange":"$$$","telephone":"1234567","address":{"@type":"PostalAddress","streetAddress":"123 William St","addressLocality":"New York","addressRegion":"NY","postalCode":"10038","addressCountry":"US"}},"reviewRating":{"@type":"Rating","ratingValue":"4"},"name":"A good seafood place.","author":{"@type":"Person","name":"Bob Smith"},"reviewBody":"The seafood is great.","publisher":{"@type":"Organization","name":"Washington Times"}}
REVIEW,
            <<<VIDEO
{"@context":"https://schema.org","@type":"VideoObject","name":"Introducing the self-driving bicycle in the Netherlands","description":"This spring, Google is introducing the self-driving bicycle in Amsterdam, the world&apos;s premier cycling city. The Dutch cycle more than any other nation in the world, almost 900 kilometres per year per person, amounting to over 15 billion kilometres annually. The self-driving bicycle enables safe navigation through the city for Amsterdam residents, and furthers Google&apos;s ambition to improve urban mobility with technology. Google Netherlands takes enormous pride in the fact that a Dutch team worked on this innovation that will have great impact in their home country.","thumbnailUrl":["https://example.com/photos/1x1/photo.jpg","https://example.com/photos/4x3/photo.jpg","https://example.com/photos/16x9/photo.jpg"],"uploadDate":"2016-03-31T08:00:00+08:00","duration":"PT1M54S","contentUrl":"https://www.example.com/video/123/file.mp4","embedUrl":"https://www.example.com/embed/123","interactionStatistic":{"@type":"InteractionCounter","interactionType":{"@type":"WatchAction"},"userInteractionCount":5647018},"regionsAllowed":"US,NL","potentialAction":{"@type":"SeekToAction","target":"https://video.example.com/watch/videoID?t={seek_to_second_number}","startOffset-input":"required name=seek_to_second_number"}}
VIDEO,
            <<<WEBPAGE
{"@context":"http://schema.org","@id":"https://www.example.com/#webpage","@type":"WebPage","url":"https://www.example.com/","name":"Example"}
WEBPAGE,
            <<<WEBSITE
{"@context":"http://schema.org","@id":"https://www.example.com/#website","@type":"WebSite","url":"https://www.example.com/","name":"Example","potentialAction":[{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"https://query.example.com/search?q={search_term_string}"},"query-input":"required name=search_term_string"},{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"android-app://com.example/https/query.example.com/search/?q={search_term_string}"},"query-input":"required name=search_term_string"}]}
WEBSITE
        ];
        ///////////////////////
        include_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $custom_schema_table = $wpdb->prefix . "scsc_custom_schemas";
        $custom_schema_sql = "CREATE TABLE $custom_schema_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            schema_type varchar(10) DEFAULT '' NOT NULL,
            post_id bigint(20) NULL,
            custom_schema varchar(10000) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $custom_schema_table)) != $custom_schema_table) :
            dbDelta($custom_schema_sql);
        endif;

        $check_schema_query = "SELECT * FROM $custom_schema_table WHERE schema_type = 'example';";
        $has_schema_setting = $wpdb->get_results($check_schema_query, ARRAY_A);
        if (!isset($has_schema_setting[0])) :
            foreach ($schema_examples as $key => $value) :
                $wpdb->insert($custom_schema_table, array("custom_schema" => serialize(str_replace('"', '&quot;', str_replace("'", '&apos;', $value))), "schema_type" => "example", "post_id" => "-1"));
            endforeach;
        endif;

        $schema_settings_table = $wpdb->prefix . "scsc_settings";
        $settings_sql = "CREATE TABLE $schema_settings_table (
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            setting_key varchar(100) DEFAULT '' NOT NULL,
            setting_value varchar(100) DEFAULT '' NOT NULL
        ) $charset_collate;";
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $schema_settings_table)) != $custom_schema_table) :
            dbDelta($settings_sql);
        endif;
        //////////////////////////////////
        /**
         * IF TABLE EXISTS, THIS PREVENTS DOUBLE ENTRIES OF INITIAL SETTINGS:
         */
        $check_active_tab_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_tab';";
        $has_active_tab_setting = $wpdb->get_results($check_active_tab_query, ARRAY_A);
        if (!isset($has_active_tab_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_tab', 'setting_value' => 'home'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => 'home'), array('setting_key' => 'active_tab'));
        endif;

        $check_active_page_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_page';";
        $has_active_page_setting = $wpdb->get_results($check_active_page_query, ARRAY_A);
        if (!isset($has_active_page_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_page', 'setting_value' => '-1'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => '-1'), array('setting_key' => 'active_page'));
        endif;

        $check_active_post_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_post';";
        $has_active_post_setting = $wpdb->get_results($check_active_post_query, ARRAY_A);
        if (!isset($has_active_post_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_post', 'setting_value' => '-1'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => '-1'), array('setting_key' => 'active_post'));
        endif;

        $check_website_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'website_schema';";
        $has_website_setting = $wpdb->get_results($check_website_query, ARRAY_A);
        if (!isset($has_website_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'website_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "website_schema"));
        endif;

        $check_webpage_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'webpage_schema';";
        $has_webpage_setting = $wpdb->get_results($check_webpage_query, ARRAY_A);
        if (!isset($has_webpage_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'webpage_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "webpage_schema"));
        endif;

        $check_breadcrumb_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'breadcrumb_schema';";
        $has_breadcrumb_setting = $wpdb->get_results($check_breadcrumb_query, ARRAY_A);
        if (!isset($has_breadcrumb_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'breadcrumb_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "breadcrumb_schema"));
        endif;

        $check_search_param_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'search_param';";
        $has_search_param_setting = $wpdb->get_results($check_search_param_query, ARRAY_A);
        if (!isset($has_search_param_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'search_param', 'setting_value' => 's'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => 'search'), array("setting_key" => "search_param"));
        endif;

        $check_yoast_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'yoast_schema';";
        $has_yoast_setting = $wpdb->get_results($check_yoast_query, ARRAY_A);
        if (!isset($has_yoast_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'yoast_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "yoast_schema"));
        endif;

        $check_aio_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'aio_schema';";
        $has_aio_setting = $wpdb->get_results($check_aio_query, ARRAY_A);
        if (!isset($has_aio_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'aio_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "aio_schema"));
        endif;
        /**
         * 
         */
    }
    /**
     * Activates the Scalpel!
     */
    public static function activate()
    {
        global $wpdb;
        if (is_multisite()) :

            $blogids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->blogs);
            foreach ($blogids as $blog_id) :

                switch_to_blog($blog_id);
                Schema_Scalpel_Activator::db_tables_initializer();
                restore_current_blog();

            endforeach;
            
        else :
            Schema_Scalpel_Activator::db_tables_initializer();
        endif;
    }
}
