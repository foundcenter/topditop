<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>TopDiTop - @yield('pageTitle')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.google-analytics')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/modal.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/transition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/dropdown.min.css') }}">

    {{-- implement cache busting minute --}}
    <?php $version = '?v=' . date("Y-m-d-H-i") ?>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/front.css') . $version }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/front-from-scss.css') . $version }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') . $version }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/slavisa.css') . $version }}">

    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        _globalLang = "{{$locale}}";
        if (_globalLang == "")
            _globalLang = "en";

        _globalRoute = "{{ url('/') }}";
    </script>

    @yield("header")
</head>

<body class="front">

@include('front.partials.modal.registration')
@include('front.partials.modal.login')

@include('front.partials.components.header')

@include('dashboard.partials.component.flash-statuses')

@yield("content")

@include('dashboard.partials.component.footer')

<script type="text/javascript" src="{{ asset('assets/js/lib/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/jquery-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/switchery.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/lib/transition.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/dropdown.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/dimmer.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lib/modal.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/lang.dist.js') }}"></script>
<script>
    Lang.setLocale(_globalLang);
</script>

<script type="text/javascript" src="{{ asset('js/macy.js') }}"></script>
<script>
    var macyInstance = Macy({
        container: '.brandreferences-macy',
        columns: 3,
        margin: {
            x: 10,
            y: 40
        },
        breakAt: {
            940: 2,
            640: 1
        }
    });
    macyInstance.reInit();
</script>

<script src="{{ asset('assets/js/script.js')}}"></script>

@if(session('success') == 'showmodalplease')
    <script>
        jQuery('.login-modal-open').trigger('click');
    </script>
@endif

@if($errors->has('failed_login') && $errors->get('failed_login'))
    <script>
        jQuery('.login-modal-open').trigger('click');
    </script>
@endif

@yield("footer")
@include("partials.analytics")
</body>
</html>
