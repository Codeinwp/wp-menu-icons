/**
 * wp.media.model.MenuIconsItemSettingField
 *
 * @class
 * @augments Backbone.Model
 */
var MenuIconsItemSettingField = Backbone.Model.extend({
	defaults: {
		id:    '',
		label: '',
		value: '',
		type:  'text'
	}
});

module.exports = MenuIconsItemSettingField;
