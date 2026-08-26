{{--
    So sánh xe. Bảng cuộn ngang trong khung riêng, không đẩy cả trang trôi.

    Ô nào xe không khai thì để dấu gạch — không mượn giá trị của xe bên cạnh.

    Biến: $cars · $rows · $all
--}}
@extends('frontend.layout', ['title' => 'So sánh xe'])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li>So sánh</li>
        </ol>
    </div>

    <section class="block" style="padding-top:32px">
        <div class="wrap">
            <h1>So sánh xe</h1>

            <form class="compare-pick" method="GET" action="{{ route('compare') }}">
                <span class="field__label">Chọn xe để so sánh (tối đa 3)</span>

                <div class="compare-pick__grid">
                    @foreach ($all as $car)
                        <label class="pick {{ $cars->contains('slug', $car->slug) ? 'is-on' : '' }}">
                            <input type="checkbox" name="xe[]" value="{{ $car->slug }}"
                                   @checked($cars->contains('slug', $car->slug))>
                            <b>{{ $car->name }}</b>
                        </label>
                    @endforeach
                </div>

                <button class="btn btn--sm" type="submit">So sánh</button>
            </form>

            @if ($cars->count() < 2)
                <p class="empty">Chọn ít nhất hai xe để đặt thông số cạnh nhau.</p>
            @else
                <div class="scroll-x">
                    <table class="compare">
                        <thead>
                            <tr>
                                <th scope="col">Thông số</th>
                                @foreach ($cars as $car)
                                    <th scope="col">
                                        <a href="{{ route('products.show', $car->slug) }}">{{ $car->name }}</a>
                                        @if ($car->price_from)
                                            <span>Từ {{ catalog_money_short($car->price_from) }}</span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <th scope="row">{{ $row['label'] }}</th>
                                    @foreach ($row['values'] as $value)
                                        <td>{{ filled($value) ? $value : '—' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
