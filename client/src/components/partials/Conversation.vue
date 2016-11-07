<template lang="jade">
.conversation
	span.conv-message {{conv.lastMessage}}
	<br>
	span {{conv.lastDate}}
</template>

<script>
export default {
	name: 'Conversation',

	props: {
		conv: Object,
	}
}
</script>

<style lang="stylus">
	.conversation
		border-bottom #D1D1D1
		
		.conv-message
			font-weight bold
</style>