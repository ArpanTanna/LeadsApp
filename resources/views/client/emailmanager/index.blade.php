@extends('layouts.client.template')

@section('content')
<div class="clearfix">
    <div class="d-flex">
        @include ('layouts.client.leftbar')
        <div class="center-container flex-grow-1">
            <div class="container-fluid py-4 px-4">
                <emailmanager-page></emailmanager-page>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        var pageLayout = 'layout1';
        var funnels = '{!!json_encode($funnels, JSON_UNESCAPED_UNICODE )!!}';
        var leads = '{!!json_encode($leads, JSON_UNESCAPED_UNICODE )!!}';
    </script>
@endsection
