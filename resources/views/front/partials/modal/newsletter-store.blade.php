<div class="ui modal modal-newsletter" style="position: relative; overflow: visible;">
    <i class="close icon"></i>
    <div class="content">

        <!-- Begin MailChimp Signup Form -->
        <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            #mc_embed_signup {
                background: #fff;
                clear: left;
                font: 14px Helvetica, Arial, sans-serif;
            }

            /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
               We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
        </style>
        <div id="mc_embed_signup">
            <form action="//topditop.us14.list-manage.com/subscribe/post?u={{$datablock['newsletter_key_1']}}&amp;id={{$datablock['newsletter_key_2']}}"
                  method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
                  target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">
                    <h2>{{ trans('messages.subscribe') }}</h2>
                    <div class="indicates-required"><span class="asterisk">*</span> {{ trans('messages.required') }}
                    </div>
                    <div class="mc-field-group">
                        <label for="mce-EMAIL">{{ trans('messages.email_address') }} <span class="asterisk">*</span>
                        </label>
                        <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                    </div>
                    <div class="mc-field-group">
                        <label for="mce-FNAME">{{ trans('messages.first_name') }}</label>
                        <input type="text" value="" name="FNAME" class="" id="mce-FNAME">
                    </div>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display:none"></div>
                        <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>
                    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_15e1f7c1ef11478c52fc7c1e6_08b4777936"
                                                                                              tabindex="-1" value="">
                    </div>
                    <div class="clear"><input type="submit" value="{{ trans('messages.sub_scribe') }}" name="subscribe"
                                              id="mc-embedded-subscribe" class="button"></div>
                </div>
            </form>
        </div>
        <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
        <script type='text/javascript'>(function ($) {
                window.fnames = new Array();
                window.ftypes = new Array();
                fnames[0] = 'EMAIL';
                ftypes[0] = 'email';
                fnames[1] = 'FNAME';
                ftypes[1] = 'text';
            }(jQuery));
            var $mcj = jQuery.noConflict(true);</script>
        <!--End mc_embed_signup-->
    </div>
</div>
