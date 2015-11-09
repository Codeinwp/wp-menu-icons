/**
 * wp.media.view.MenuIconsSidebar
 */
var MenuIconsSidebar = wp.media.view.IconPickerSidebar.extend({
	createSingle: function() {
		console.log( 'create single' );
	},
	disposeSingle: function() {}
});

module.exports = MenuIconsSidebar;
