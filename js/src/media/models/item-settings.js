/**
 * wp.media.model.MenuIconsItemSettings
 *
 * @class
 * @augments Backbone.Collection
 */
var MenuIconsItemSettings = Backbone.Collection.extend({
	model: wp.media.model.MenuIconsItemSettingField
});

module.exports = MenuIconsItemSettings;
