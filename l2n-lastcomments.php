<?php
/**
 * Plugin Name: l2n اخر التعليقات 
 * Plugin URI: http://www.lab2net.net/?p=1497
 * Description: مربع جانبي يعرض اخر التعليقات المنشورة على موقعك
 * Version: 1.0
 * Author: Samy lab2net
 * Author URI: http://www.lab2net.net
 */
class L2N_last_Comments extends WP_Widget {

	function L2N_last_Comments() {
		// widget actual processes		
		/* Widget settings. */
		$widget_ops = array('classname' => 'widget_l2n_last_comments', 'description' => 'مربع جانبي يعرض اخر التعليقات المنشورة على موقعك' );
		/* Create the widget. */
		$this->WP_Widget( 'L2N_last_Comments', 'L2N اخر التعليقات', $widget_ops, $control_ops );
	}
	function flush_widget_cache() {
		wp_cache_delete('widget_last_comments', 'widget');
	}
	



/***************the widjet**************/
	function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = wp_cache_get('widget_l2n_last_comments', 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

 		extract($args, EXTR_SKIP);
 		$output = '';
 		$title = apply_filters('widget_title', empty($instance['title']) ? __('last Comments') : $instance['title']);

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 5;

		$comments = get_comments( array( 'type' => 'comment', 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) );
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;

		$output .= '<ul id="lastcomments">';
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
				$comment_txt=get_comment_text();
				$output .=  '<li class="lastcomments">'.get_comment_author().' : <a href="' . get_comment_link($comment->comment_ID) . '">' . l2n_rctext( $comment_txt, '10'). '</a></li>';
			}
 		}
		$output .= '</ul>';
		$output .= $after_widget;

		echo $output;
		$cache[$args['widget_id']] = $output;
		wp_cache_set('widget_last_comments', $cache, 'widget');
	}


/***************update**************/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_l2n_last_comments']) )
			delete_option('widget_l2n_last_comments');

		return $instance;
	}
	
/*****************admin************/
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
} 

  function l2n_rctext($theContent, $num ) {  
	$output = preg_replace('/<img[^>]+./','', $theContent);  
	$limit = $num+1;  
	$content = explode(' ', $output, $limit);  
	array_pop($content);  
	$content = implode(" ",$content);  
	$content = strip_tags($content, '<p><a><address><a><abbr><acronym><b><big><blockquote><br><caption><cite><class><code><col><del><dd><div><dl><dt><em><font><h1><h2><h3><h4><h5><h6><hr><i><img><ins><kbd><li><ol><p><pre><q><s><span><strike><strong><sub><sup><table><tbody><td><tfoot><tr><tt><ul><var>');
	$content .= " ...";
	$commenttxt=fix_tags($content);
	return $commenttxt;
	}
//Fix unclosed xhtml tags
function fix_tags($text) {
    $patt_open    = "%((?<!</)(?<=<)[\s]*[^/!>\s]+(?=>|[\s]+[^>]*[^/]>)(?!/>))%";
    $patt_close    = "%((?<=</)([^>]+)(?=>))%";

    if (preg_match_all($patt_open,$text,$matches))
    {
        $m_open = $matches[1];
        if(!empty($m_open))
        {
            preg_match_all($patt_close,$text,$matches2);
            $m_close = $matches2[1];
            if (count($m_open) > count($m_close))
            {
                $m_open = array_reverse($m_open);
                foreach ($m_close as $tag) $c_tags[$tag]++;
                foreach ($m_open as $k => $tag)    if ($c_tags[$tag]--<=0) $text.='</'.$tag.'>';
            }
        }
    }
    return $text;
}

// register L2N_LastPosts widget
add_action( 'widgets_init', create_function( '', 'return register_widget("L2N_last_Comments");' ) );
?>
