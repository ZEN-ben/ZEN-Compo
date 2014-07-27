/**
 * Delay
 * http://stackoverflow.com/questions/1909441/jquery-keyup-delay
 */
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

/**
 * Applies a mask to a single element to prevent a user from using it and to indicate there is an operation busy.
 * Usage: $('.container').loading(); // apply mask
 *        $('.container').loading('hide'); // restore the container (by removing the mask)
 */
;
jQuery.fn.loading = function (mixed) {

    if (this.length > 1) {
throw new Error('More then one element supplied'); }

    if (mixed == 'hide') {
        var $mask = this.find('.load-mask')
        var me = this;
        $mask.fadeOut(function () {
            $mask.remove();
            if (me.attr('data-old-position') == 'static') {
                me.css('position', '');
                me.removeAttr('data-old-position');
            }
        });
        return this;
    }
    if (this.find('.load-mask').length > 0) {
return this; }
    var $loaderDOM = jQuery('<div class="load-mask" style="opacity:0;cursor:wait;position:absolute;top:0;right:0;bottom:0;left:0;background:white;"><div class="ajaxloader" style="margin:0 auto; margin-top:100px;"></div></div>');
    if (this.parent().css('position') == 'static') {
        this.attr('data-old-position', this.css('position'));
        this.css('position', 'relative');
    }
    this.append($loaderDOM);

    // center the loading icon
    var h = $loaderDOM.innerHeight();
    if (h > 80) {
        $loaderDOM.find('.ajaxloader').addClass('ajaxloader-big');
    }
    var top = $.isPlainObject(mixed) && mixed.hasOwnProperty('top') ? true : false;
    var pos = top ? mixed.top : (h / 2) - 9;
    this.find('.ajaxloader').css('margin-top', pos);
    $loaderDOM.animate({
        opacity: 0.8
    });
    return this;
};

function notify(title, message) {
    if (window.webkitNotifications.checkPermission() !== 0) {
        $('body').one('click', function () {
            window.webkitNotifications.requestPermission();
        });
    } else {
        notification = window.webkitNotifications.createNotification('icon.png', title, message);
        notification.show();
    }
}
