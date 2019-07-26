@extends('layouts.client.template')

@section('content')
    <div class="clearfix">
        <div class="d-flex">
            @include ('layouts.client.leftbar')
            <div class="center-container w-100 align-self-stretch">
                <div class="container-fluid py-4 px-4">
                    <div class="container">
                        <div class="clearfix">
                            <example-component></example-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var pageLayout = 'layout1';
    </script>
@endsection
