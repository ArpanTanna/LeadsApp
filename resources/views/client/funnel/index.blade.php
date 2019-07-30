@extends('layouts.client.template')

@section('content')
<div class="clearfix">
    <div class="d-flex">
        @include ('layouts.client.leftbar')
        <div class="center-container flex-grow-1">
            <div class="container-fluid py-4 px-4">
                <funnel-page></funnel-page>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        var pageLayout = 'layout1';
        var funnels = '{!!json_encode($funnels, JSON_UNESCAPED_UNICODE )!!}';
    </script>
@endsection
