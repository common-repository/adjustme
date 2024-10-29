var adjustme = new Vue({
el: '#adjustme_app',
data: {
    name: 'Adjustme',
    list: (adjustme_vars.post.post_meta.adjustme_list ? JSON.parse(adjustme_vars.post.post_meta.adjustme_list) : []),
    pending: (adjustme_vars.post.post_meta.adjustme_pending ? JSON.parse(adjustme_vars.post.post_meta.adjustme_pending) : 'false'),
    loading: 'false',
    notification: {
        type: '',
        message: ''
    }
},
//Watch changes and save them to server
watch: {
    list: {
        handler: function (list) {
            this.save_list(list);
        },
        deep: true
    },
    pending: {
        handler: function (data) {
            adjustme.ajax_post('adjustme_save_pending', JSON.stringify(data));
        },
        deep: true
    },
    notification: {
        handler: function () {
            window.setTimeout(this.notification_cleanup,5000);
        },
        deep: true
    }
},
methods: {
    activation: function(){
        console.log('Run Forrest run!');
        this.debug(adjustme_vars.post);

        jQuery(document).ready(function($) {

            $(window).resize(function(){
                alert('Please "Send to developer" before resize. Otherwise it will screw up the box placement.')
            });

            $('body').addClass('adjustme_active');

            function escape(e){
                if (e.keyCode == 27) {
                    adjustme.deactivation();

                    //Unbind to avoid multiples
                    $(document).unbind("keyup", escape);
                }
            }
            $(document).keyup(escape);

        });

        //If list is empty
        if (this.list.length == 0) {
            this.place_request();
        }
        if(this.pending == 'true'){
            adjustme.notify('message', 'These request has been sent to a developer.');
        }
    },
    deactivation: function(){
        console.log('Hide Forrest hide!');

        jQuery(document).ready(function($) {
            $(window).off("resize");

            $('body').removeClass('adjustme_active');
            $('body').removeClass('adjustme_pin_active');
            $('#adjustme_toolbar_add').removeClass('adjustme_active');
        })
    },
    add_request: function(text, x, y){
        adjustme.debug('Adding request to adjustme list');

        request = {
            text: text,
            position: {
                left: x + 'px',
                top: y + 'px'
            }
        };

        this.list.push(request);

    },
    remove_request: function(request){
        adjustme.debug('Removing current request');

        Vue.delete(adjustme.list, this.list.indexOf(request));
    },
    place_request: function(){
        adjustme.debug('Placing request');

        jQuery(document).ready(function($) {
            $('#adjustme_toolbar_add').addClass('adjustme_active');
            $('body').addClass('adjustme_pin_active');
            $('body').unbind();
            $('#adjustme_list .adjustme_request').removeClass('adjustme_focus');

            $('body').bind('mousedown', function (event) {
                console.log(event.target);
                if ($(event.target).closest('#wpadminbar').length || $(event.target).closest('textarea').length || $(event.target).closest('.adjustme_request_bar').length || $(event.target).closest('#adjustme_toolbar').length) {
                    return;
                }

                //If clicked element is not on the wpadminbar or another request
                event.preventDefault();
                adjustme.add_request('', event.pageX, event.pageY);

                $('#adjustme_toolbar_add').removeClass('adjustme_active');
                $('body').removeClass('adjustme_pin_active');
                $('body').unbind();

                jQuery(document).ready(function($) {
                    //Put in que, shitty callback :-)
                    setTimeout(function() {
                        $('#adjustme_list .adjustme_request:last-child').addClass('adjustme_focus');
                        $('#adjustme_list .adjustme_request:last-child textarea').focus();
                    }, 0);
                });

            });
        });
    },
    open_request: function(event){
        adjustme.debug('Open request');
        jQuery(document).ready(function($) {
            if($(event.currentTarget).closest('.adjustme_request').hasClass('adjustme_focus')){
            }else{
                $(event.currentTarget).closest('.adjustme_request').addClass('adjustme_focus')
            }
        });
    },
    minimize_request: function(event){
        adjustme.debug('Minimize request');
        jQuery(document).ready(function($) {
            adjustme.debug($(event.currentTarget).parents('.adjustme_request'));
            $(event.currentTarget).parents('.adjustme_request').removeClass('adjustme_focus');
        });
    },
    move_request: function(request){
        adjustme.debug('Moving request');

        var this_request = this;
        var this_request_index = this_request.list.indexOf(request);

        jQuery(document).ready(function($) {
            adjustme.debug('asd');
            $(this).bind('mousemove', function(event) {
                if ($(event.target).closest('.adjustme_request_remove').length) {
                    return;
                }

                Vue.set(adjustme.list[this_request_index].position, 'left' , event.pageX + 'px');
                Vue.set(adjustme.list[this_request_index].position, 'top' , event.pageY + 'px');

            });

            $(this).bind('mouseup', function() {
                $(this).unbind();
            });
        });
    },
    //Debounce trottles the save input when it called multiple times. Then it won't fire until the last called function is X milliseconds old
    save_list: jQuery.debounce(1000, function(){
        adjustme.debug('Saving list');

        this.ajax_post('adjustme_save_list', JSON.stringify(adjustme.list));
    }),
    post_list: jQuery.debounce(1000, function(){
        adjustme.debug('Posting list');

        Vue.set(adjustme, 'loading' , 'true');

        var send_data = {
            browser: {
                url: window.location.href,
                domain: window.location.hostname,
                user_agent: navigator.userAgent,
                window_height: jQuery(window).height(),
                window_width: jQuery(window).width()
            },
            list:  JSON.stringify(adjustme.list)
        };

        this.ajax_post('adjustme_server_save_list', send_data, adjustme_vars.server_url);
    }),
    ajax_post: function(ajax_function, data, url){
        var url = (typeof url !== 'undefined') ?  url : '/wp-admin/admin-ajax.php';

        console.log(data);
        adjustme.debug('Ajax post function fired');

        jQuery(document).ready(function($){
            $.post(url,{
                action: ajax_function,
                post: adjustme_vars.post,
                settings: adjustme_vars.settings,
                data: data
            })
            .fail(function(response){
                console.log(response);
                adjustme.notify('error', 'Could not contact server.');
            })
            .done(function(response){
                if(response == 0){
                    adjustme.notify('error', 'Server response not valid.');
                }else if(response == 'request true'){
                    adjustme.notify('message', 'Sent to server.');
                    $('.adjustme_request').fadeOut();
                    adjustme.pending = 'true';
                }
                console.log(response);
            })
            .always(function(){
                Vue.set(adjustme, 'loading' , 'false');
            });
        });
    },
    debug: function(text){

        if(adjustme_vars.debug){
            var d = new Date();
            console.log(d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds() );

            (typeof text !== 'undefined') ?  console.log(text) : '';
        }

    },
    notify: function(type, message){
        Vue.set(adjustme.notification, 'type' , type);
        Vue.set(adjustme.notification, 'message' , message);
    },
    notification_cleanup: jQuery.debounce(5000, function (){
        Vue.set(adjustme.notification, 'type' , '');
        window.setTimeout(function(){
            Vue.set(adjustme.notification, 'message' , '');
        },300);
    })

}
});