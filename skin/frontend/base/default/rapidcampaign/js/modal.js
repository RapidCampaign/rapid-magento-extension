var PromotionModal = Class.create();
PromotionModal.prototype = {

    initialize: function(iframeId, delay, width) {

        this.iframeId = '#' + iframeId;
        this.width = width;
        this.delay = delay * 1000;

        if (!this.hasCookie()){
            this.load();
        }
    },

    load: function () {
        Custombox.open({
            target: this.iframeId,
            effect: 'fadein',
            width: this.getWidth(),
            overlaySpeed: this.delay,
            loading: {
                delay: this.delay,
            }
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

    getHeight: function(modalHeight) {
        if (!modalHeight) {
            modalHeight = 'auto';
        }

        return modalHeight;
    },

    hasCookie: function() {
        return false;
    },

    setCookie: function (name, expires) {
            var cookieStr = name + "=" + escape(1) + "; ";
            if (expires) {
                var expiresDate = new Date(new Date().getTime() + expires * 24 * 60 * 60 * 1000);
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

