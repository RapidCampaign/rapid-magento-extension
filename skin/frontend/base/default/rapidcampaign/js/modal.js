var PromotionModal = Class.create();
PromotionModal.prototype = {

    initialize: function(iframeId, delay, width, cookieName, cookieExpires) {

        this.iframeId = iframeId;
        this.delay = delay * 1000; // in seconds
        this.width = width;
        this.getWidth();
        this.cookieName = cookieName;
        this.cookieExpires = cookieExpires;

        if (!this.hasCookieSet()){
            this.load();
        }
    },

    load: function () {
        promotionModal = this;
        setTimeout(function() {
            Custombox.open({
                target: "#" + promotionModal.iframeId,
                effect: 'fadein',
                width: promotionModal.width,
                close: function() {
                    promotionModal.setCookie();
                },
            });

            var html = '<a href="#" class="modal-close" onclick="Custombox.close();">Close</a>';
            $(promotionModal.iframeId).insert({
                top: html
            });

        }, this.delay);
    },

    getWidth: function() {
        if (!this.width) {
            var dims = document.viewport.getDimensions();
            this.width = dims.width * 0.9;
            if (this.width > 1000) {
                this.width = 1000;
            }
        }

        return
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

