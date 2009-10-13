/* vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb: */
/**
 * Ghoul - Simple MicoBlog
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-10-10
 * @link   http://www.gracecode.com/
 */

window.addEvent('domready', function() {
    var container = $('show');
    var buildIcon = function () {
        $each(container.getElementsByTagName('li'), function(c){
            var id = c.id.match(/^micro_(\d+)$/)[1] || false;
            if (id && !c.getElementsByTagName('a').length) {
                var link = new Element('a', {
                    'rel': id,
                    'title': 'Delte this entry',
                    'class': 'delete',
                    'html': '[Del]',
                    'href': ''
                });
                c.appendChild(link);
            }
        });
    };
    // <?php if (IS_LOGIN) { echo "\n buildIcon();"; } echo "\n"; ?>

    container.addEvent('click', function(e){
        var target = e.target;
        if (target.hasClass('delete') && target.nodeName.toLowerCase() && target.rel && e.stop() && confirm('Are you sure, Master?')) {
            var req = new Request({
                method: 'get', url: 'delete/' + target.rel + '/',
                onSuccess: function(responseText) {
                    if (responseText == '0') {
                        return;
                    }

                    var el = $('micro_' + target.rel);
                    var effect = new Fx.Morph(el, {duration: 'long', 
                        transition: Fx.Transitions.Sine.easeOut,
                        onComplete: function() {
                            container.removeChild(el);
                        }
                    });
                    effect.start({'opacity': 0});
                }
            });
            req.send();
        }
    });

    var _loading = false;
    $(window).addEvent('scroll', function(e){
        if (_loading) return;
        if (document.getSize().y + document.getScroll().y >= document.getScrollSize().y) {
            var page = parseInt(container.getAttribute('current:page'), 10) + 1;
            var uri = 'show/?format=json&page=' + page;
            var req = new Request({
                method: 'get', url: uri,
                onRequest: function() {
                    $('loading').setStyle('visibility', 'visible');
                },
                onComplete: function() {
                    $('loading').setStyle('visibility', 'hidden');
                },
                onSuccess: function(responseText) {
                    if (responseText) {
                        var data = JSON.decode(responseText);
                        for (var i = 0, len = data.length; i < len; i++) {
                            var item = new Element('li', {
                                'id': 'micro_' + data[i]['id'],
                                'html': '<span class="date">'+data[i]['date']+'</span>'+data[i]['data']
                            });
                            container.appendChild(item);
                        };
                        container.setAttribute('current:page', page);
                    }
                    //! <?php if (IS_LOGIN) { echo "\n buildIcon();"; } echo "\n"; ?>
                    if (len) _loading = false;
                }
            });
            _loading = true; req.send();
        }
    });

    $(document.body).appendChild(new Element('div', {
        'id': 'loading',
        'html': 'Loading...'
    }));

    if (Browser.Engine.trident) {
        $$('#show li').addEvent('mouseenter', function(e) {
            this.addClass('hover');
        });

        $$('#show li').addEvent('mouseleave', function(e) {
            this.removeClass('hover');
        });
    }
});
