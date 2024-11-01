<?php
/**
 * Class-yummy-recipe.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Recipe.
 */
class Yummy_Recipe {

	/**
	 * WP_Post object.
	 *
	 * @var WP_Post
	 */
	public $post;

	/**
	 * Post id.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	private $attributes;

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Taxonomies.
	 *
	 * @var array
	 */
	public $taxonomies;

	/**
	 * Ingredient lists.
	 *
	 * @var array
	 */
	public $ingredient_lists;

	/**
	 * Average rating.
	 *
	 * @var string
	 */
	public $average_rating;

	/**
	 * Ratings count.
	 *
	 * @var string
	 */
	public $ratings_count;

	/**
	 * Prep time.
	 *
	 * @var string
	 */
	public $prep_time;

	/**
	 * Prep time formatted.
	 *
	 * @var string
	 */
	public $prep_time_formatted;

	/**
	 * Cook time.
	 *
	 * @var string
	 */
	public $cook_time;

	/**
	 * Cook time formatted.
	 *
	 * @var string
	 */
	public $cook_time_formatted;

	/**
	 * Yield.
	 *
	 * @var string
	 */
	public $yield;

	/**
	 * Servings
	 *
	 * @var string
	 */
	public $servings;

	/**
	 * Calories per serving.
	 *
	 * @var float
	 */
	public $calories_per_serving;

	/**
	 * Description.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Instructions.
	 *
	 * @var string
	 */
	public $instructions;

	/**
	 * Video URL.
	 *
	 * @var string
	 */
	public $video_url;

	/**
	 * Image id.
	 *
	 * @var int
	 */
	public $image_id;

	/**
	 * Author name.
	 *
	 * @var string
	 */
	public $author_name;

	/**
	 * __construct.
	 *
	 * @param WP_Post $get_post    WP_Post object.
	 * @param array   $attributes  Attributes.
	 */
	public function __construct( $get_post, $attributes ) {
		$this->post       = $get_post;
		$this->post_id    = $get_post->ID;
		$this->attributes = $attributes;

		$this->set_title();
		$this->set_taxonomies();
		$this->set_ingredient_lists();
		$this->set_instructions();
		$this->set_average_rating();
		$this->set_ratings_count();
		$this->set_author_name();
		$this->set_prep_time();
		$this->set_prep_time_formatted();
		$this->set_cook_time();
		$this->set_cook_time_formatted();
		$this->set_yield();
		$this->set_servings();
		$this->set_calories_per_serving();
		$this->set_description();
		$this->set_video_url();
		$this->set_image_id();
	}

	/**
	 * Return post id.
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Set recipe title.
	 * Use the post title if there is no block attribute set for the title.
	 */
	public function set_title() {
		$this->title = ( ! empty( $this->attributes['title'] ) ? $this->attributes['title'] : get_the_title( $this->post_id ) );
	}

	/**
	 * Get recipe title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set ingredient lists.
	 */
	public function set_ingredient_lists() {

		$this->ingredient_lists = array();

		$ingredients_meta = ( ! empty( $this->attributes['ingredients'] ) ? $this->attributes['ingredients'] : '' );

		if ( ! empty( $ingredients_meta ) ) {
			$ingredients = json_decode( $ingredients_meta, false );

			$list_id = 0;
			foreach ( $ingredients as $key => $ingredient ) {

				if ( ! empty( $ingredient->title ) ) {
					$list_id++;
					$list = new StdClass();

					$list->title       = $ingredient->title;
					$list->ingredients = array();
				}

				if ( ! empty( $ingredient->name ) ) {
					$single_ingredient = new StdClass();

					$single_ingredient->name = ( is_rtl() ? '&#8207;' . $ingredient->name : $ingredient->name );
					$single_ingredient->unit = ( ! empty( $ingredient->unit ) ? $ingredient->unit : '' );
					$single_ingredient->amount = ( ! empty( $ingredient->amount ) ? $ingredient->amount : '' );
					$single_ingredient->amount_numeric = '';
					$single_ingredient->amount_type = '';

					if ( ! empty( $ingredient->amount ) ) {
						$amount_type = yummy_get_amount_type( $ingredient->amount );
						$numeric_value = yummy_get_numeric_values( $ingredient->amount, $amount_type );

						$single_ingredient->amount_numeric = $numeric_value;
						$single_ingredient->amount_type = $amount_type;
					}

					if ( empty( $list ) ) {
						$list = new StdClass();
					}

					if ( empty( $list->ingredients ) ) {
						$list->ingredients = array();
					}

					$list->ingredients[] = $single_ingredient;
				}

				// Add only if there are ingredients.
				if ( ! empty( $list->ingredients ) || ! empty( $list->title ) ) {
					$this->ingredient_lists[ $list_id ] = $list;
				}
			}
		}
	}

	/**
	 * Get ingredient lists.
	 *
	 * @return array
	 */
	public function get_ingredient_lists() {
		return $this->ingredient_lists;
	}

	/**
	 * Set taxonomies.
	 */
	public function set_taxonomies() {

		$this->taxonomies = array();

		// Get the taxonomies displayed on the card.
		$taxonomies_on_card = get_option( 'yummy_taxonomies_on_card', array( 'yummy_course', 'yummy_difficulty', 'yummy_special_diet' ) );

		$all_taxonomies = get_object_taxonomies( 'yummy_recipe', 'objects' );

		// Check if the post has a taxonomy, and insert the taxonomy to the returned object.
		if ( ! empty( $taxonomies_on_card ) && ! empty( $all_taxonomies ) ) {
			foreach ( $all_taxonomies as $tax ) {
				if ( in_array( $tax->name, $taxonomies_on_card, true ) && has_term( '', $tax->name, $this->post_id ) ) {
					$this->taxonomies[] = $tax;
				}
			}
		}
	}

	/**
	 * Get taxonomies.
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		return $this->taxonomies;
	}

	/**
	 * Set instructions.
	 */
	public function set_instructions() {
		$this->instructions = ( ! empty( $this->attributes['instructions'] ) ? $this->attributes['instructions'] : '' );
	}

	/**
	 * Get instructions.
	 *
	 * @return string
	 */
	public function get_instructions() {
		return $this->instructions;
	}

	/**
	 * Set average rating.
	 */
	public function set_average_rating() {
		$this->average_rating = get_post_meta( $this->post_id, '_yummy_meta_average_rating', true );
	}

	/**
	 * Get average rating.
	 *
	 * @return string
	 */
	public function get_average_rating() {
		if ( is_numeric( $this->average_rating ) ) {
			return $this->average_rating;
		}

		return null;
	}

	/**
	 * Set ratings count.
	 */
	public function set_ratings_count() {
		$this->ratings_count = get_post_meta( $this->post_id, '_yummy_meta_ratings_count', true );
	}

	/**
	 * Get ratings count.
	 *
	 * @return string
	 */
	public function get_ratings_count() {
		return $this->ratings_count;
	}

	/**
	 * Set author name.
	 */
	public function set_author_name() {
		$this->author_name = get_the_author_meta( 'display_name', $this->post->post_author );
	}

	/**
	 * Get author name.
	 *
	 * @return string
	 */
	public function get_author_name() {
		return $this->author_name;
	}

	/**
	 * Set prep time.
	 */
	public function set_prep_time() {
		$this->prep_time = ( ! empty( $this->attributes['prepTime'] ) ? $this->attributes['prepTime'] : '' );
	}

	/**
	 * Get prep time.
	 *
	 * @return string
	 */
	public function get_prep_time() {
		return $this->prep_time;
	}

	/**
	 * Set prep time formatted.
	 */
	public function set_prep_time_formatted() {
		$this->prep_time_formatted = ( ! empty( $this->attributes['prepTime'] ) ? yummy_minutes_to_hr_min( $this->attributes['prepTime'] ) : '' );
	}

	/**
	 * Get prep time formatted.
	 *
	 * @return string
	 */
	public function get_prep_time_formatted() {
		return $this->prep_time_formatted;
	}

	/**
	 * Set cook time.
	 */
	public function set_cook_time() {
		$this->cook_time = ( ! empty( $this->attributes['cookTime'] ) ? $this->attributes['cookTime'] : '' );
	}

	/**
	 * Get cook time.
	 *
	 * @return string
	 */
	public function get_cook_time() {
		return $this->cook_time;
	}

	/**
	 * Set cook time formatted.
	 */
	public function set_cook_time_formatted() {
		$this->cook_time_formatted = ( ! empty( $this->attributes['cookTime'] ) ? yummy_minutes_to_hr_min( $this->attributes['cookTime'] ) : '' );
	}

	/**
	 * Get cook time formatted.
	 *
	 * @return string
	 */
	public function get_cook_time_formatted() {
		return $this->cook_time_formatted;
	}

	/**
	 * Set yield.
	 */
	public function set_yield() {
		$this->yield = ( ! empty( $this->attributes['yield'] ) ? $this->attributes['yield'] : '' );
	}

	/**
	 * Get yield.
	 *
	 * @return string
	 */
	public function get_yield() {
		return $this->yield;
	}

	/**
	 * Set servings.
	 */
	public function set_servings() {
		if ( ! empty( $this->attributes['nutrition'] ) && ! empty( $this->attributes['nutrition']['servingSize'] ) ) {
			$this->servings = $this->attributes['nutrition']['servingSize'];
		} else {
			$this->servings = '';
		}
	}

	/**
	 * Get servings.
	 *
	 * @return string
	 */
	public function get_servings() {
		return $this->servings;
	}

	/**
	 * Set calories per serving.
	 */
	public function set_calories_per_serving() {
		if ( ! empty( $this->attributes['nutrition'] ) && ! empty( floatval( $this->attributes['nutrition']['calories'] ) ) ) {
			$this->calories_per_serving = floatval( $this->attributes['nutrition']['calories'] );
		} else {
			$this->calories_per_serving = '';
		}
	}

	/**
	 * Get calories per serving.
	 *
	 * @return float
	 */
	public function get_calories_per_serving() {
		return $this->calories_per_serving;
	}

	/**
	 * Set description.
	 */
	public function set_description() {
		$this->description = ( ! empty( $this->attributes['description'] ) ? $this->attributes['description'] : '' );
	}

	/**
	 * Get description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set video URL.
	 */
	public function set_video_url() {
		$this->video_url = ( ! empty( $this->attributes['video_url'] ) ? $this->attributes['video_url'] : '' );
	}

	/**
	 * Get video URL.
	 *
	 * @return string
	 */
	public function get_video_url() {
		return $this->video_url;
	}

	/**
	 * Set image id.
	 */
	public function set_image_id() {
		$this->image_id = false;

		$default_thumbnail_id = get_option( 'yummy_default_thumbnail_image_id' );

		if ( is_numeric( $default_thumbnail_id ) ) {
			$this->image_id = $default_thumbnail_id;
		}

		$thumbnail_id = get_post_thumbnail_id( $this->post_id );

		if ( $thumbnail_id ) {
			$this->image_id = $thumbnail_id;
		}

		if ( ! empty( $this->attributes['mediaID'] ) ) {
			$this->image_id = $this->attributes['mediaID'];
		}
	}

	/**
	 * Get image id.
	 *
	 * @return int
	 */
	public function get_image_id() {
		return $this->image_id;
	}

	/**
	 * Get nutrition facts.
	 *
	 * @param  boolean $all  If servings and calories per serving fields should be included.
	 *
	 * @return array
	 */
	public function get_nutrition_facts( $all = false ) {
		return yummy_get_nutrition_facts( $this->attributes, $all );
	}

	/**
	 * Returns true if nutrition facts, serving size, or calories per serving is set.
	 * Used to check if the nutrition facts table should be displayed.
	 *
	 * @return boolean
	 */
	public function has_nutrition_facts() {
		if ( ! empty( $this->get_nutrition_facts( true ) ) ) {
			return true;
		}

		return false;
	}
}
