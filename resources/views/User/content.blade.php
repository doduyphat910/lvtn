@extends('User.index2')

@section('content')
    <section class="content-header" style="margin-top: 50px;">
        <h1>
            {{ $header or trans('admin.title') }}
            <small>{{ $description or trans('admin.description') }}</small>
        </h1>

        <!-- breadcrumb start -->
        @if ($breadcrumb)
        <ol class="breadcrumb" style="margin-right: 30px;">
            <li><a href="{{ url('user/student') }}"><i class="fa fa-home"></i> Trang chủ</a></li>
            @foreach($breadcrumb as $item)
                @if($loop->last)
                    <li class="active">
                        @if (array_has($item, 'icon'))
                            <i class="fa fa-{{ $item['icon'] }}"></i>
                        @endif
                        {{ $item['text'] }}
                    </li>
                @else
                <li>
                    <a href="{{ admin_url(array_get($item, 'url')) }}">
                        @if (array_has($item, 'icon'))
                            <i class="fa fa-{{ $item['icon'] }}"></i>
                        @endif
                        {{ $item['text'] }}
                    </a>
                </li>
                @endif
            @endforeach
        </ol>
        @endif
        <!-- breadcrumb end -->

    </section>

    <section class="content">

        @include('User.partials.error')
        @include('User.partials.success')
        @include('User.partials.exception')
        @include('User.partials.toastr')

        {!! $content !!}

    </section>
@endsection