<script id="child-reply" type="text/template">
	<div class="items-thread clearfix child">
		<div class="f-floatleft">
			<img class="avatar avatar-64" alt="" src="{{= author.avatar.thumb64 }}">
		</div>
		<div class="f-floatright clearfix">
			<div class="name">
				{{= author.display_name }}
				<span class="like"><span data-icon="k" class="icon"></span>0</span>
			</div>
			<div class="content">
				{{= post_content }}
			</div>
		</div>
	</div>
</script>