define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/translate',
    'priceUtils',
    'priceBox'
], function ($, _, mageTemplate, $t, priceUtils) {
    'use strict';
    $.widget('mage.cmsLinkToMenuTargetBlank', {
        options: {
            selector: '.level-top',
            open_in_new_tab: 0,
            links: []
        },

        /** @inheritdoc */
        _create: function () {
            var self = this, subMenus,
                linkList = this.options.links;

            if(this.options.open_in_new_tab == 0){
                return this._super();
            }
            subMenus = this.element.find(this.options.selector);
            $.each(subMenus, $.proxy(function (index, item) {
                var category = $(item).find('> a span').not('.ui-menu-icon').text(),
                    categoryUrl = $(item).find('> a').attr('href'),
                    menu = $(item).find('> .ui-menu');
                if (categoryUrl !== 'undefined') {
                    var results;
                    results = linkList.filter(item => item.link_url == categoryUrl);
                    if (results.length > 0) {
                        $(item).find('> a').attr('target', '_blank');
                    }
                }

            }, this));
            return this._super();
        }
    });
    return $.mage.cmsLinkToMenuTargetBlank;
});
