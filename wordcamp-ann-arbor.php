<?php
/*
Plugin Name: WordCamp Ann Arbor
Plugin URI: http://2014.annarbor.wordcamp.org
Description: Creates a widget for rendering badges
Author: Topher
Version: 1.2.1
Author URI: http://topher1kenobe.com
"WordCamp Ann Arbor" is released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: wordcamp-badges
*/

/**
 * Adds AnnArbor_Badge_Widget widget.
 */
class AnnArbor_Badge_Widget extends WP_Widget {

	private $wordcamp_ann_arbor_badges = array();
	private $old_vals					  = array();

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'annarbor_badge_widget',
			__( 'Ann Arbor WordCamp Badges', 'wordcamp-badges' ),
			array( 'description' => __( 'Renders Ann Arbor WordCamp Badges', 'wordcamp-badges' ), )
		);

		$this->wordcamp_ann_arbor_badges = array(
			'speaker_small_badge'	=> 'Small Speaker',
			'speaker_large_badge'	=> 'Large Speaker',
			'attendee_small_badge'	=> 'Small Attendee',
			'attendee_large_badge'	=> 'Large Attendee',
			'volunteer_small_badge' => 'Small Volunteer',
			'volunteer_large_badge' => 'Large Volunteer',
			'sponsor_small_badge'	=> 'Small Sponsor',
			'sponsor_large_badge'	=> 'Large Sponsor',
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'annarbor_badge_widget_styles' ) );

	}

	/**
	 * Enqueue a few styles
	 *
	 * returns NULL
	 */
	public function annarbor_badge_widget_styles() {
		wp_enqueue_style( 'annarbor-badge-widget', plugins_url( 'css/wordcamp-ann-arbor.css' , __FILE__ ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args	  Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		echo '<ul>' . "\n";

		foreach ( $instance as $key => $val ) {
			if ( $val == 1 ) {
				echo '<li class="' . wp_kses_post( $key ) . '"><a href="http://2014.annarbor.wordcamp.org/"><img src="' . esc_url( plugins_url( 'images/' . $key . '.png ', __FILE__ ) ) . '" /></a></li>';
			}
		}

		echo '</ul>' . "\n";

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Ann Arbor WordCamp', 'wordcamp-badges' );
		}

		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p><strong>Choose Badges</strong></p>

		<ul>

			<?php
				if ( is_array( $this->wordcamp_ann_arbor_badges ) ) {
					foreach ( $this->wordcamp_ann_arbor_badges as $image_prefix => $image_name ) {

						$this->old_vals[ $image_prefix ] = isset( $instance[ $image_prefix ] ) ? absint( $instance[ $image_prefix ] ) : '';

						echo '<li>' . "\n";
							echo '<input id="' . $this->get_field_id( $image_prefix ) . '" name="' . $this->get_field_name( $image_prefix ) . '" type="checkbox" value="1" ' . checked( '1', $this->old_vals[ $image_prefix ], false ) . ' />' . "\n";
							echo '<label for="' . $this->get_field_id( $image_prefix ) . '">' . $image_name . '</label>' . "\n";
						echo '</li>' . "\n";
					}
				}
			?>

		</ul>


		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		if ( is_array( $this->wordcamp_ann_arbor_badges ) ) {
			foreach ( $this->wordcamp_ann_arbor_badges as $image_prefix => $image_name ) {
				$instance[ $image_prefix ] = ( ! empty( $new_instance[ $image_prefix ] ) ) ? strip_tags( $new_instance[ $image_prefix ] ) : '';
			}
		}

		return $instance;
	}

} // class AnnArbor_Badge_Widget

// register AnnArbor_Badge_Widget widget
function register_annarbor_badge_widget() {
	register_widget( 'AnnArbor_Badge_Widget' );
}
add_action( 'widgets_init', 'register_annarbor_badge_widget' );
