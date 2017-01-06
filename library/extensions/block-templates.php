<?php
/**
 * Block Templates Extensions
 *
 * @package ThematicCoreLibrary
 * @subpackage Block Templates Extenstions
 */

if ( ! function_exists( 'thematic_block_template' ) ) {
	
	/**
	 * Automagically include a block template based on the given section and current query result
	 *
	 * @param string $section The section (folder) to use when finding templates
	 * @param string $content Optional. Any existing content that we need to reference (for example, inside a filter).
	 *
	 * @return boolean|mixed False if no template was loaded, any other value as returned from that template (if applicable).
	 */
	function thematic_block_template( $section, $content = null ) {
		if ( empty( $section ) ) {
			return false;
		}

		$blocks = array();

		if ( is_404() ) {
			$blocks[] = '404.php';
		}

		if ( is_home() ) {
			$blocks[] = 'home.php';
		}

		if ( is_front_page() ) {
			$blocks[] = 'front_page.php';
		}

		if ( is_home() || is_front_page() ) {
			$blocks[] = 'front.php';
		}
		
		if ( is_page() ) {
			$obj = get_queried_object();
				
			if ( ! empty( $obj->ID ) ) {
				$blocks[] = "page-{$obj->ID}.php";
			}
				
			if ( ! empty( $obj->name ) ) {
				$blocks[] = "page-{$obj->name}.php";
			}
			
			$template = get_page_template_slug( get_queried_object_id() );
			if ( ! empty( $template ) ) {
				$template  = basename( $template );
				if ( ! empty( $template ) ) {
					$blocks[] = 'page-' . $template;
				}
			}
				
			$blocks[] = 'page.php';
		} else if ( is_attachment() ) {
			$obj = get_queried_object();
					
			if ( ! empty( $obj->ID ) ) {
				$blocks[] = "attach-{$obj->ID}.php";
			}
			
			if ( ! empty( $obj->name ) ) {
				$blocks[] = "attach-{$obj->name}.php";
			}
				
			$template = get_page_template_slug( get_queried_object_id() );
			if ( ! empty( $template ) ) {
				$template  = basename( $template );
				if ( ! empty( $template ) ) {
					$blocks[] = 'attach-' . $template;
				}
			}

			$blocks[] = 'attach.php';
		} else if ( is_single() ) {
			$obj = get_queried_object();
				
			if ( ! empty( $obj->ID ) ) {
				$blocks[] = "single-{$obj->ID}.php";
			}
			
			if ( ! empty( $obj->name ) ) {
				$blocks[] = "single-{$obj->name}.php";
			}
				
			if ( ! empty( $obj->post_type ) ) {
				$blocks[] = "single-{$obj->post_type}.php";
			}
				
			$template = get_page_template_slug( get_queried_object_id() );
			if ( ! empty( $template ) ) {
				$template  = basename( $template );
				if ( ! empty( $template ) ) {
					$blocks[] = $template;
				}
			}
				
			$blocks[] = 'single.php';
		} else if ( is_category() ) {
			$obj = get_queried_object();
				
			if ( ! empty( $obj->slug ) ) {
				$blocks[] = "category-{$obj->term_id}.php";
				$slug_decoded = urldecode( $obj->slug );
				if ( $slug_decoded !== $obj->slug ) {
					$blocks[] = "category-{$slug_decoded}.php";
				}
				$blocks[] = "category-{$obj->slug}.php";
			}
			$blocks[] = 'category.php';
		} else if ( is_archive() ) {
			$type = get_post_type();
				
			if ( ! empty( $type ) ) {
				$blocks[] = "archive-{$type}.php";
			}
				
			$blocks[] = 'archive.php';
		}

		$blocks[] = 'default.php';

		print "\n<!-- block_inclusion({$section})\n";
		print "blocks=".substr( print_r( $blocks, true ), 7, -3 ) . "\n";
		//print "------content------\n{$content}\n----end content----\n";
		print "-->";

		foreach ( $blocks as $block ) {
			if ( empty( $block ) ) {
				continue;
			}
			$path = path_join( get_stylesheet_directory(), 'blocks' );
			$path = path_join( $path, $section );
			$path = path_join( $path, $block );

			if ( file_exists( $path ) ) {
				$r = include $path;
				//print "<!-- \n------return------\n{$r}\n----end return----\n -->";
				if ( $r === 1 ) {
					if ( ! is_null( $content ) ) {
						return $content;
					}
					return true;
				}
				if ( $r !== false ) {
					return $r;
				}
				if ( ! is_null( $content ) ) {
					return $content;
				}
			}
		}

		if ( ! is_null( $content ) ) {
			return $content;
		}
		return false;
	}
}
