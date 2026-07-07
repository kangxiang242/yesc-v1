@extends(is_mobile_domain()?"mobile.layout":"web.layout")

@section('track-init')
<script>Track.init({ platform: '{{ is_mobile_domain() ? "mobile" : "web" }}', page_type: 'not_found' });</script>
@endsection

@section('title', "Not Found")
@section('keywords', "")
@section('description', "")
@section('banners')@stop
@section('style')
    @parent
<style>
    /*html, body {
        background-color: #fff;
        color: #636b6f;
        font-family: 'Nunito', sans-serif;
        font-weight: 100;
        height: 100vh;
        margin: 0;
    }*/


    .full-height {
        min-height: calc(100vh - 464px);
    }

    .flex-center {
        align-items: center;
        display: flex;
        justify-content: center;
    }

    .position-ref {
        position: relative;
    }

    .code {
        border-right: 2px solid;
        font-size: 26px;
        padding: 0 15px 0 15px;
        text-align: center;
    }

    .message {
        font-size: 18px;
        text-align: center;
    }
</style>
@stop
@section('script')
    @parent
    <script>
        $('#banner').remove();
        $('.parallax_holder').remove();
    </script>
@stop

@section('content')
<div class="flex-center position-ref full-height" data-track-block="{{ is_mobile_domain() ? 'm_404_content' : 'web_404_content' }}">
    <div class="code">404</div>

    <div class="message" style="padding: 10px;">Not Found</div>
</div>
@endsection
