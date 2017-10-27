<?php 
et_get_mobile_header();
// header part
get_template_part( 'mobile/template', 'header' );
?>
<div data-role="content" class="fe-content fe-content-auth">
</div>
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>