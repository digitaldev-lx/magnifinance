<style>
    .cookie-consent{
        position: fixed;
        bottom: 0;
        z-index: 99999;
        background: {{ $frontThemeSettings->primary_color }};
        line-height: 20%;
        height: 20%;
        width: 100%;

    }

    .content{
        width: 100%;
        text-align: center;
        margin: 0;
        position: absolute;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    @media only screen and (max-width: 600px) {
        .content{
            width: unset;
        }
        .cookie-consent{
            padding: 10px;
            height: 30%;
        }
    }
</style>

<div class="js-cookie-consent cookie-consent">

    <div class="content">
        <p class="cookie-consent__message">
            {!! trans('cookieConsent::texts.message') !!}
        </p>

        <button class="js-cookie-consent js-cookie-consent-agree btn btn-primary-outline mt-2">
            {{ trans('cookieConsent::texts.agree') }}
        </button>
    </div>


</div>
