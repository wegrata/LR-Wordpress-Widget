<?php

class LRInterfaceSearch extends WP_Widget
{
  function LRInterfaceSearch()
  {
    $widget_ops = array('classname' => 'LRInterfaceSearch', 'description' => 'Adds an LR search bar to a page' );
    $this->WP_Widget('LRInterfaceSearch', 'LR Interface Search Bar', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'placeholder'=>'', 'type' => '', 'hide' => '', 'label' => '' ) );
    $title = $instance['title'];
	$placeholder = $instance['placeholder'];
	$type = $instance['type'];
	$hide = $instance['hide'];
	$label = $instance['label'];
?>

<p>

	<label for="<?php echo $this->get_field_id('title'); ?>">
		Title: 
	</label>	
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
	<br/><br/>
	<label for="<?php echo $this->get_field_id('placeholder'); ?>">
		Search text: 
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo attribute_escape($placeholder); ?>" />
	<br/><br/>
	
	<label for="<?php echo $this->get_field_id('label'); ?>">
		Check to display search text as a label instead of a placeholder: 
	</label>
	<input class="widefat" <?php echo $label == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="checkbox" />
	<br/><br/>
	
	<label for="<?php echo $this->get_field_id('type'); ?>">
		Search Method:
	</label>
	<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
		<option value="index" <?php echo attribute_escape($type) == "index" ? 'selected="selected"':''; ?>>Indexed Search</option>
		<option value="slice" <?php echo attribute_escape($type) == "slice" ? 'selected="selected"':''; ?>>Slice</option>
	</select>
	<br/><br/>	
	
	<label for="<?php echo $this->get_field_id('hide'); ?>">
		Check to show this widget only on results and preview pages: 
	</label>
	<input class="widefat" <?php echo $hide == 'on' ? 'checked' : ''; ?> id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" />
	<br/><br/>
	
</p>
  
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['placeholder'] = $new_instance['placeholder'];
	$instance['type'] = $new_instance['type'];
	$instance['hide'] = $new_instance['hide'];
	$instance['label'] = $new_instance['label'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
	if($instance['hide'] == 'on' && (empty($_GET['query']) && empty($_GET['lr_resource'])))
		return;

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : $instance['title'];
    $type = empty($instance['type']) ? 'index' : $instance['type'];
    $label = empty($instance['label']) ? false : $instance['label'];
    $placeholder = $instance['placeholder'];
	$options = get_option('lr_options_object');
	
	
    if (!empty($title))
      echo $before_title . $title . $after_title;
		
	?>
	<form method="get" id="LRsearchForm" action="<?php echo get_page_link( $options['results'] ); ?>">
		<?php if(!empty($placeholder) && !empty($label)): ?>
			<label for="lrSearchBox" style="margin-bottom:6px;display:block;"><?php echo $placeholder; ?></label>
		<?php endif; ?>
		<div class="lrSearchCombo">
			<input id="lrSearchBox" class="lrSearch" type="text" title="<?php echo $instance['placeholder']; ?>" name="query" <?php echo (empty($placeholder)||!empty($label))?'':'placeholder="' . $placeholder . '"'; ?> />			
			<input class="lrSubmit" type="submit" value="Search" />
		</div>
		<input id="lrGovCheck" type="checkbox"></input> <span style="font-weight:bold;">Federal Resources Only</span>
	</form>
	
	
	<script type="text/javascript">
		jQuery(document).ready(function($){
			
			$('.lrSearch').data('holder',$('.lrSearch').attr('placeholder'));
			
			$('.lrSearch').focusin(function(){
				$(this).attr('placeholder','');
			});
			$('.lrSearch').focusout(function(){
				$(this).attr('placeholder',$(this).data('holder'));
			});
			
			$("#LRsearchForm").submit(function(e){
				e.preventDefault();
				var search = encodeURIComponent($("#LRsearchForm input").val());
				var gov = $("#lrGovCheck").attr("checked");
				var searchUrl = '<?php echo add_query_arg("query", "LRreplaceMe", get_page_link( $options['results']));?>'.replace("LRreplaceMe", search + '&type=<?php echo $type; ?>');
				if(gov){
					searchUrl += "&gov=1"
				}
				window.location.href = searchUrl;
			});
		});
	</script>
	<?php
    echo $after_widget;
  }
 
}