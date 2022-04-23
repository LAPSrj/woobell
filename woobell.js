(function($) {
    let woobell_ringing = false;
    let audio = new Audio(woobell.sound_url);

    function woobell_init(){
        if(sessionStorage.getItem('woobell')) return false;

        let data = {
            'action': 'woobell_last_order',
        };
        
        $.ajax({
            url: woobell.ajax_url,
            data: data,
            success: function(order){
                order = parseInt(order);
                
                if(order == order){
                    sessionStorage.setItem('woobell', order);
                }
            }
        });
    }

    function woobell_check(){
        if(woobell_ringing) return false;
        if(!sessionStorage.getItem('woobell')) return false;

        let data = {
            'action': 'woobell_last_order',
            'processing': true
        };
        
        $.ajax({
            url: woobell.ajax_url,
            data: data,
            success: function(order){
                order = parseInt(order);
                lastOrder = parseInt(sessionStorage.getItem('woobell'));
                
                if(order == order && order > lastOrder){
                    woobell_new_order_alert(order);
                    sessionStorage.setItem('woobell', order);
                }
            }
        });

    }

    function woobell_new_order_alert(order){
        woobell_ringing = true;
        let url = new URL(woobell.admin_url);
        url.searchParams.set('post', order);
        url.searchParams.set('action', 'edit');
        $('.woobell-notification .woobell-yes').attr('href', url.toString());
        $('.woobell-notification').fadeIn();
        audio.currentTime = 0;
        audio.loop = true;
        audio.play();
    }

    function woobell_dismiss(){

        $('.woobell-notification').fadeOut();
        audio.pause();
        woobell_ringing = false;
    }

    $(document).ready(function(){
        woobell_init();
        setInterval(woobell_check, 60000);

        $('.woobell-no').on('click', function(e){
            e.preventDefault();
            woobell_dismiss();
        });
    });
})( jQuery );