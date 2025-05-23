<?php

class Skyword_Sitemaps {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'skyword_sitemaps_generator' ) );
	}

	/**
	 * Intercepts all requests and checks if requesting sitemaps or robots.txt
	 */
	public function skyword_sitemaps_generator() {
		$options = get_option( 'skyword_plugin_options' );
		//check all requests for if they are for autogenerated robots.txt or sitemaps
		$request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
		if  ( strpos($request_uri, 'skyword-sitemap.xml') !== false) {
			if ( $options['skyword_generate_all_sitemaps'] ) {
				header( 'Content-type: text/xml' );
				// Below function returns appropriately sanitized XML
				// phpcs:ignore
				print $this->skyword_generate_all_sitemaps();
				die;
			} else {
				return;
			}
		} else if ( strpos($request_uri, 'skyword-pages-sitemap.xml') !== false) {
			if ( $options['skyword_generate_pages_sitemaps'] ) {
				header( 'Content-type: text/xml' );
				// Below function returns appropriately sanitized XML
				// phpcs:ignore
				print $this->skyword_generate_pages_sitemaps();
				die;
			} else {
				return;
			}
		} else if ( strpos($request_uri, 'skyword-categories-sitemap.xml') !== false) {
			if ( $options['skyword_generate_categories_sitemaps'] ) {
				header( 'Content-type: text/xml' );
				// Below function returns appropriately sanitized XML
				// phpcs:ignore
				print $this->skyword_generate_categories_sitemaps();
				die;
			} else {
				return;
			}
		} else if ( strpos($request_uri, 'skyword-tags-sitemap.xml') !== false) {
			if ( $options['skyword_generate_tags_sitemaps'] ) {
				header( 'Content-type: text/xml' );
				// Below function returns appropriately sanitized XML
				// phpcs:ignore
				print $this->skyword_generate_tags_sitemaps();
				die;
			} else {
				return;
			}
		} else if ( strpos($request_uri, 'skyword-google-news-sitemap.xml') !== false) {
			if ( $options['skyword_generate_news_sitemaps'] ) {
				header( 'Content-type: text/xml' );
				// Below function returns appropriately sanitized XML
				// phpcs:ignore
				print $this->skyword_generate_google_news_sitemaps();
				die;
			} else {
				return;
			}
			die;
		} else if ( strpos($request_uri, 'robots.txt') !== false) {
			header( 'Content-type: text/plain' );
			print "User-agent: * \n";
			print "Disallow: /wp-admin/ \n";
			print "Disallow: /wp-includes/  \n";
			$options = get_option( 'skyword_plugin_options' );
			if ( $options['skyword_generate_all_sitemaps'] ) {
				print "Sitemap: " . esc_url(get_site_url()) . "/skyword-sitemap.xml \n";
			}
			if ( $options['skyword_generate_pages_sitemaps'] ) {
				print "Sitemap: " . esc_url(get_site_url()) . "/skyword-pages-sitemap.xml \n";
			}
			if ( $options['skyword_generate_categories_sitemaps'] ) {
				print "Sitemap: " . esc_url(get_site_url()) . "/skyword-categories-sitemap.xml \n";
			}
			if ( $options['skyword_generate_tags_sitemaps'] ) {
				print "Sitemap: " . esc_url(get_site_url()) . "/skyword-tags-sitemap.xml \n";
			}
			if ( $options['skyword_generate_news_sitemaps'] ) {
				print "Sitemap: " . esc_url(get_site_url()) . "/skyword-google-news-sitemap.xml \n";
			}
			die;
		}
	}

	/**
	 * Generates all post sitemaps
	 */
	private function skyword_generate_all_sitemaps() {
		$args = array(
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'numberposts'      => 100
		);

		$rows = get_posts( $args );

		$xmlOutput = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ( $rows as $row ) {
			$xmlOutput .= "\t<url>\n";
			$xmlOutput .= "\t\t<loc>";
			$xmlOutput .= esc_html( get_permalink( $row->ID ) );
			$xmlOutput .= "</loc>\n";

			$xmlOutput .= "\t\t<priority>";
			$xmlOutput .= "0.9";
			$xmlOutput .= "</priority>\n";
			$xmlOutput .= "\t\t<changefreq>";
			$xmlOutput .= "yearly";
			$xmlOutput .= "</changefreq>\n";
			$xmlOutput .= "\t</url>\n";
		}
		// End urlset
		$xmlOutput .= '</urlset>';

		return $xmlOutput;

	}

	/**
	 * Generates page only sitemaps
	 */
	private function skyword_generate_pages_sitemaps() {
		$args = array(
			'post_type'        => 'page',
			'suppress_filters' => false,
			'numberposts'      => 100
		);
		$rows = get_posts( $args );

		$xmlOutput = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ( $rows as $row ) {
			$xmlOutput .= "<url>";
			$xmlOutput .= "<loc>";
			$xmlOutput .= esc_html( get_site_url() . "/" . $row->post_name );
			$xmlOutput .= "</loc>";

			$xmlOutput .= "<priority>";
			$xmlOutput .= "0.9";
			$xmlOutput .= "</priority>";
			$xmlOutput .= "<changefreq>";
			$xmlOutput .= "yearly";
			$xmlOutput .= "</changefreq>";
			$xmlOutput .= "</url>";
		}

		$xmlOutput .= "</urlset>";

		return $xmlOutput;
	}

	/**
	 * Generates category page sitemaps
	 */
	private function skyword_generate_categories_sitemaps() {
		$args = array(
			'type'         => 'post',
			'child_of'     => 0,
			'parent'       => '',
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'exclude'      => '',
			'include'      => '',
			'number'       => '',
			'taxonomy'     => 'category',
			'pad_counts'   => false
		);
		$rows = get_categories( $args );

		$xmlOutput = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ( $rows as $row ) {
			$xmlOutput .= '<url>';
			$xmlOutput .= '<loc>';
			$xmlOutput .= esc_html( get_category_link( $row->cat_ID ) );
			$xmlOutput .= '</loc>';

			$xmlOutput .= '<priority>';
			$xmlOutput .= '0.9';
			$xmlOutput .= '</priority>';
			$xmlOutput .= '<changefreq>';
			$xmlOutput .= 'yearly';
			$xmlOutput .= '</changefreq>';
			$xmlOutput .= '</url>';
		}

		$xmlOutput .= '</urlset>';

		return $xmlOutput;
	}

	/**
	 * Generates tag page sitemaps
	 */
	private function skyword_generate_tags_sitemaps() {
		$rows      = get_tags();
		$xmlOutput = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ( $rows as $row ) {
			$xmlOutput .= '<url>';
			$xmlOutput .= '<loc>';
			$xmlOutput .= esc_html( get_tag_link( $row->term_id ) );
			$xmlOutput .= '</loc>';

			$xmlOutput .= '<priority>';
			$xmlOutput .= '0.9';
			$xmlOutput .= '</priority>';
			$xmlOutput .= '<changefreq>';
			$xmlOutput .= 'yearly';
			$xmlOutput .= '</changefreq>';
			$xmlOutput .= '</url>';
		}

		$xmlOutput .= '</urlset>';

		return $xmlOutput;
	}

	/**
	 * Generate google news sitemaps
	 */
	private function skyword_generate_google_news_sitemaps() {
		$args = array(
			'post_type'        => 'post',
			'date_query'       => array(
				array(
					'column' => 'post_date_gmt',
					'after'  => '48 hours ago',
				),
			),
			'meta_query'       => array(
				array(
					'key'   => 'skyword_publication_type',
					'value' => 'news',
				)
			),
			'suppress_filters' => false
		);
		$rows = get_posts( $args );

		$xmlOutput = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
		// Output sitemap data
		foreach ( $rows as $row ) {

			//Sets news:name
			if ( null !== get_metadata( "post", $row->ID, 'publication-name', true ) ) {
				$newsName = get_metadata( "post", $row->ID, 'publication-name', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_publication_name', true ) ) {
				$newsName = get_metadata( "post", $row->ID, 'skyword_publication_name', true );
			} else {
				$newsName = get_option( 'blogname' );
			}

			//Sets news:access values
			if ( null !== get_metadata( "post", $row->ID, 'publication-access', true ) ) {
				$newsAccess = get_metadata( "post", $row->ID, 'publication-access', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_publication_access', true ) ) {
				$newsAccess = get_metadata( "post", $row->ID, 'skyword_publication_access', true );
			}

			//Sets news:geo_locations
			if ( null !== get_metadata( "post", $row->ID, 'publication-geolocation', true ) ) {
				$newsGeoLocations = get_metadata( "post", $row->ID, 'publication-geolocation', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_publication_geolocation', true ) ) {
				$newsGeoLocations = get_metadata( "post", $row->ID, 'skyword_publication_geolocation', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_geolocation', true ) ) {
				$newsGeoLocations = get_metadata( "post", $row->ID, 'skyword_geolocation', true );
			}

			//Set news:stock_tickers
			if ( null !== get_metadata( "post", $row->ID, 'publication-stocktickers', true ) ) {
				$newsStockTickers = get_metadata( "post", $row->ID, 'publication-stocktickers', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_publication_stocktickers', true ) ) {
				$newsStockTickers = get_metadata( "post", $row->ID, 'skyword_publication_stocktickers', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_stocktickers', true ) ) {
				$newsStockTickers = get_metadata( "post", $row->ID, 'skyword_stocktickers', true );
			}

			//Set news:keywords
			if ( null !== get_metadata( "post", $row->ID, 'publication-keywords', true ) ) {
				$newsKeywords = get_metadata( "post", $row->ID, 'publication-keywords', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_publication_keywords', true ) ) {
				$newsKeywords = get_metadata( "post", $row->ID, 'skyword_publication_keywords', true );
			} else if ( null !== get_metadata( "post", $row->ID, 'skyword_tags', true ) ) {
				$newsKeywords = get_metadata( "post", $row->ID, 'skyword_tags', true );
			}

			$xmlOutput .= '<url>';

			$xmlOutput .= '<loc>';
			$xmlOutput .= esc_html( get_permalink( $row->ID ) );
			$xmlOutput .= '</loc>';

			$xmlOutput .= '<news:news>';
			$xmlOutput .= '<news:publication>';

			$xmlOutput .= '<news:name>';
			$xmlOutput .= esc_html( $newsName );
			$xmlOutput .= '</news:name>';

			$xmlOutput .= '<news:language>';
			$xmlOutput .= esc_html( substr( get_bloginfo( 'language' ), 0, 2 ) );
			$xmlOutput .= '</news:language>';

			$xmlOutput .= '</news:publication>';

			if ( isset( $newsAccess ) ) {
				$xmlOutput .= '<news:access>';
				$xmlOutput .= esc_html( $newsAccess );
				$xmlOutput .= '</news:access>';
			}

			if ( isset( $newsGeoLocations ) ) {
				$xmlOutput .= '<news:geo_locations>';
				$xmlOutput .= esc_html( $newsGeoLocations );
				$xmlOutput .= '</news:geo_locations>';
			}

			if ( isset( $newsStockTickers ) ) {
				$xmlOutput .= '<news:stock_tickers>';
				$xmlOutput .= esc_html( $newsStockTickers );
				$xmlOutput .= '</news:stock_tickers>';
			}
			$xmlOutput .= '<news:publication_date>';
			$xmlOutput .= esc_html( substr( $row->post_date_gmt, 0, 10 ) );
			$xmlOutput .= '</news:publication_date>';

			$xmlOutput .= '<news:title>';
			$xmlOutput .= esc_html( $row->post_title );
			$xmlOutput .= '</news:title>';

			if ( isset( $newsKeywords ) ) {
				$xmlOutput .= '<news:keywords>';
				$xmlOutput .= esc_html( $newsKeywords );
				$xmlOutput .= '</news:keywords>';
			}

			$xmlOutput .= '</news:news>';
			$xmlOutput .= '</url>';

		}
		// End urlset
		$xmlOutput .= '</urlset>';

		return $xmlOutput;
	}

}

global $skyword_sitemaps;
$skyword_sitemaps = new Skyword_Sitemaps;