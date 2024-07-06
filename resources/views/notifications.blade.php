@extends("layout.main")

@section("title", "Dashboard")
@section("warn_active", "active")

@section("content")
@php
    $notifications = App\Models\Warning::orderBy("id", "desc")->paginate(20);
@endphp
<div>
    @foreach($notifications as $warning)
    @if ($warning->change === "exp_warn" )
        @php
            $content = "Product " . "<b>" . $warning->product->name . "</b>" . " " . " Discount is about to expired ";
        @endphp
        <p style="padding: 8px;background: #80808029;font-size: 14px;display: grid;justify-content: space-between;align-items: center;gap: 16px;grid-template-columns: repeat(8, 1fr)">
            {!! $content !!}
            <span></span>
            <span></span>
            <span></span>
            <a href="/warning/delete/{{$warning->id}}" class="btn btn-sm btn-danger m-0 m-0">Delete warning</a>
            <a href="{{$warning->product->url}}" target="_blank" class="btn btn-sm btn-success m-0 m-0">Show Product</a>
        </p>
    @else
        @if ($warning->change == 'discount_value')
            @php
                $content = "Product " . "<b>" . $warning->product->name . "</b>" . " " . "Discount Value" . " has changed from ";
                $content .= "<b>";
                    $content .= $warning->change == "stock" ? ($warning->old == 1 ? "In Stock" : ($warning->old == 2 ? "Managed Stock" : "Out Of Stock")) : $warning->old;
                    $content .= "</b>";
                    $content .= " to ";
                    $content .= "<b>";
                        $content .= $warning->change == "stock" ? ($warning->new == 1 ? "In Stock" : ($warning->new == 2 ? "Managed Stock" : "Out Of Stock")) : $warning->new;
                        $content .= "</b>";

                        @endphp
            <p style="padding: 8px;background: #80808029;font-size: 14px;display: grid;justify-content: space-between;align-items: center;gap: 16px;grid-template-columns: repeat(8, 1fr)">
                {!! $content !!}
                <a href="/warning/delete/{{$warning->id}}" class="btn btn-sm btn-danger m-0 m-0">Delete warning</a>
                <a href="{{$warning->product->url}}" target="_blank" class="btn btn-sm btn-success m-0 m-0">Show Product</a>
            </p>
            @else
            @php
                $content = "Product " . "<b>" . $warning->product->name . "</b>" . " " . $warning->change . " has changed from ";
                $content .= "<b>";
                    $content .= $warning->change == "stock" ? ($warning->old == 1 ? "In Stock" : ($warning->old == 2 ? "Managed Stock" : "Out Of Stock")) : $warning->old;
                    $content .= "</b>";
                    $content .= " to ";
                    $content .= "<b>";
                        $content .= $warning->change == "stock" ? ($warning->new == 1 ? "In Stock" : ($warning->new == 2 ? "Managed Stock" : "Out Of Stock")) : $warning->new;
                        $content .= "</b>";

                        @endphp
            <p style="padding: 8px;background: #80808029;font-size: 14px;display: grid;justify-content: space-between;align-items: center;gap: 16px;grid-template-columns: repeat(8, 1fr)">
                {!! $content !!}
                <a href="/warning/delete/{{$warning->id}}" class="btn btn-sm btn-danger m-0 m-0">Delete warning</a>
                <a href="{{$warning->product->url}}" target="_blank" class="btn btn-sm btn-success m-0 m-0">Show Product</a>
            </p>
        @endif
    @endif
    @endforeach
    <div class="d-flex justify-content-center">
        {{ $notifications->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
