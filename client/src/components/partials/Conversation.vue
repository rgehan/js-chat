<template lang="jade">
.conversation(@click="selectConv(conv.conv_id)")
	span.conv-message {{conv.lastMessage}}
	<br>
	span {{conv.lastDate}}
</template>

<script>
export default {
	name: 'Conversation',

	props: {
		conv: Object,
	},
	methods: {
		selectConv: function(id){
			this.$emit('selectConvRequested', id);
		}
	}
}
</script>

<style lang="stylus">
	.conversation
		display block
		border-bottom 1px solid #D1D1D1
		color #AAA9AA
		padding 6px 8px
		
		white-space nowrap
		overflow hidden
		text-overflow ellipsis
		
		cursor pointer

		&:hover
			color #AAA9AA
		
		.conv-message
			color #463f43
</style>