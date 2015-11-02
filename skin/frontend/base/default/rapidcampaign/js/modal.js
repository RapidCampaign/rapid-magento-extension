var PromotionModal = Class.create();
PromotionModal.prototype = {

    initialize: function(iframeId, delay, width, cookieName, cookieExpires) {

        this.iframeId = iframeId;
        this.delay = delay * 1000; // in seconds
        this.width = width;
        this.cookieName = cookieName;
        this.cookieExpires = cookieExpires;

        if (!this.hasCookieSet()){
            this.load();
        }
    },

    load: function () {
        var promotionModal = this;
        Custombox.open({
            target: "#" + this.iframeId,
            effect: 'fadein',
            width: this.getWidth(),
            close: function() {
                promotionModal.setCookie();
            },
        });

        /**
         *
         * @todo Replace with appropriate Mage JS function to get local files
         */
        var imageUrl = 'skin/frontend/base/default/rapidcampaign/images/x.svg';

        var html = '<a href="#" onclick="Custombox.close();"><img src="'+imageUrl+'", class="modal-close", width="15px", style="position: absolute; right: 0;" ></a>';
        $(this.iframeId).insert({
            top: html
        });
},

    getWidth: function() {
        if (!this.width) {
            var dims = document.viewport.getDimensions();
            this.width = dims.width * 0.9;
            if (this.width > 1000) {
                this.width = 1000;
            }
        }

        return this.width;
    },

    hasCookieSet: function() {
        if (document.cookie.indexOf(this.cookieName) >= 0)
            return true;

        return false;
    },

    setCookie: function () {
            var cookieStr = this.cookieName + "=" + escape(1) + "; ";
            if (this.cookieExpires) {
                var expiresDate = new Date(new Date().getTime() + parseInt(this.cookieExpires) * 24 * 60 * 60 * 1000);
                cookieStr += "expires=" + expiresDate.toGMTString() + "; ";
            }
            if (Mage.Cookies.path) {
                cookieStr += "path=" + Mage.Cookies.path + "; ";
            }
            if (Mage.Cookies.domain) {
                cookieStr += "domain=" + Mage.Cookies.domain + "; ";
            }
            if (Mage.Cookies.domain.secure) {
                cookieStr += "secure; ";
            }
            document.cookie = cookieStr;
        }
};

