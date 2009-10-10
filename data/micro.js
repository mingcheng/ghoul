/* vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb: */
/**
 * Ghoul - Simple MicoBlog
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-10-10
 * @link   http://www.gracecode.com/
 */

window.addEvent('domready', function() {
    $each($('show').getElementsByTagName('li'), function(c){
        var id = c.id.match(/^micro_(\d+)$/)[1] || false;
        if (id) {
            var link = new Element('a', {
                'rel': id,
                'title': '删除这条信息',
                'class': 'delete',
                'html': '[删除]',
                'href': ''
            });
            c.appendChild(link);
        }
    });

    $('show').addEvent('click', function(e){
        var target = e.target;
        if (target.hasClass('delete') && target.nodeName.toLowerCase() && target.rel && e.stop() && confirm('您确认删除此条信息吗？')) {
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
                            $('show').removeChild(el);
                        }
                    });
                    effect.start({'opacity': 0});
                }
            });
            req.send();
        }
    });

    if (Browser.Engine.trident) {
        $$('#show li').addEvent('mouseenter', function(e) {
            this.addClass('hover');
        });

        $$('#show li').addEvent('mouseleave', function(e) {
            this.removeClass('hover');
        });
    }
});
