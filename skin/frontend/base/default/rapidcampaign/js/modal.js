var PromotionModal = Class.create();
PromotionModal.prototype = {

    initialize: function(iframeId, delay, width, cookieName, cookieExpires) {

        this.iframeId = iframeId;
        this.delay = delay * 1000; // in seconds
        this.width = width;
        this.cookieName = cookieName;
        this.cookieExpires = cookieExpires;

        if (!this.hasCookieSet() && this.isCompatible()){
            window.addEventListener('load', this.load.bind(this), false);
        }
    },

    /*
     * Modal window is buggy on older version of Android using the stock browser
     * We are not going to provide these browsers with the promotion.
     */
    isCompatible: function() {
        var androidMatch = window.navigator.userAgent.match(/Android.*AppleWebKit\/([\d.]+)/);
        var isStockAndroid = (androidMatch && androidMatch[1]<537);
        return !(isStockAndroid && false === 'AudioNode' in window);
    },

    load: function () {
        var promotionModal = this;
        setTimeout(function() {
            Custombox.open({
                target: "." + promotionModal.iframeId,
                effect: 'fadein',
                width: promotionModal.getWidth()
            });

            var html = '<a href="#" class="modal-close" onclick="Custombox.close();">Close</a>';
            document.querySelector('.custombox-modal').insert({
                top: html
            });

            promotionModal.setCookie();

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

        return this.width;
    },

    hasCookieSet: function() {
        return (document.cookie.indexOf(this.cookieName) >= 0);
    },

    setCookie: function () {
            var cookieStr = this.cookieName + "=1;";
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
