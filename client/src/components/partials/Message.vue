<template lang="jade">
  .message
    span.message-text {{msg.message}}
    br
    span.message-author {{msg.pseudo}}
</template>

<script>
export default {
	name: 'Message',

	props: {
		msg: Object,
	}
}
</script>

<style lang="stylus">
	.message-text, .message-author
		display inline-block
	
	.message-text
		border-radius 10px
		padding 4px 8px
	
	.author-text
		font-size 0.75em
		margin-top 0px
		
	.message
		text-align left
		.message-text
			background #DFDFDF
		.author
			margin-left 5px
			margin-right 5px
			
	.own-message
		text-align right
		.message-text
			background #0070FF
			color white
</style>