@extends("layout.main")

@section("title", "Dashboard")

@section("content")
<div id="dash_wrapper">
    <div class="row d-none">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Money</p>
                <h5 class="font-weight-bolder mb-0">
                  $53,000
                  <span class="text-success text-sm font-weight-bolder">+55%</span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Users</p>
                <h5 class="font-weight-bolder mb-0">
                  2,300
                  <span class="text-success text-sm font-weight-bolder">+3%</span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">New Clients</p>
                <h5 class="font-weight-bolder mb-0">
                  +3,462
                  <span class="text-danger text-sm font-weight-bolder">-2%</span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Sales</p>
                <h5 class="font-weight-bolder mb-0">
                  $103,430
                  <span class="text-success text-sm font-weight-bolder">+5%</span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @php
      $warnings = App\Models\Warning::all();
  @endphp
      <!-- Modal HTML -->
      @if($warnings->count() > 0)
    <div class="modal fade" id="warningModalOld" tabindex="-1" role="dialog" aria-labelledby="warningModalOldLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="  min-width: calc(100vw - 32px);">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="warningModalOldLabel">Old Warnings</h5>
                    <button type="button" class="closeOld" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="warningMessages">
                    @foreach($warnings as $warning)
                    @if ($warning->change === "exp_warn" )
                        @php
                            $content = "Product " . "<b>" . $warning->product->name . "</b>" . " " . " Discount is about to expired ";
                        @endphp
                        <p style="padding: 8px;background: #80808029;font-size: 14px;">
                            {!! $content !!}
                            <a href="" class="text-danger bold remove_warning" style="padding: 0 12px;font-weight: bold;" warning_id="{{ $warning->id }}">Remove</a>
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
                            <p style="padding: 8px;background: #80808029;font-size: 14px;">
                                {!! $content !!}
                                <a href="" class="text-danger bold remove_warning" style="padding: 0 12px;font-weight: bold;" warning_id="{{ $warning->id }}">Remove</a>
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
                            <p style="padding: 8px;background: #80808029;font-size: 14px;">
                                {!! $content !!}
                                <a href="" class="text-danger bold remove_warning" style="padding: 0 12px;font-weight: bold;" warning_id="{{ $warning->id }}">Remove</a>
                            </p>
                        @endif
                    @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="removeAllWarningsButton">Remove All</button>
                    <button type="button" class="btn btn-secondary closeOld" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
          <button type="button" class="close" style="background: transparent; border: none" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('product.store')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" v-for="(item, index) in urls" :key="index">
                    <label for="">Url <span v-if="index != 0" @click="removeUrl(index)" class="text-danger" style="cursor: pointer"> Remove</span></label>
                    <input type="text" name="url[]" id="url" placeholder="Product Url" class="form-control">
                </div>
                <button class="btn btn-success w-100" @click.prevent="this.urls.push([''])">Add Another url</button>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="site" value="1" checked id="flexRadioDefault1">
                    <label class="form-check-label" for="flexRadioDefault1">
                      Costco UK
                    </label>
                  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
    @if($errors->first("url"))
        <div class="alert alert-danger text-light mt-4">
        {{ $errors->first('url') }}
        </div>
    @endif
    @if($errors->first("general"))
        <div class="alert alert-danger text-light mt-4">
        {{ $errors->first('general') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

  <div class="row mt-4">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between">
          <h6>All Products</h6>
          <form id="delete-all" method="POST" action="/delete-all-selected">
            @csrf
            <input type="hidden" name="products[]" v-for="item in selected_products" :value="item">
          </form>
          <div class="d-flex" style="gap: 12px">
              <button class="btn btn-danger" v-if="selected_products.length > 0" @click="deleteAll">Delete selected</button>
              <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Add Product</button>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                <th></th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Price</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Value Price</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stock</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                    <div class="d-flex align-items-center" style="gap: 8px">
                        Stock Level
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $sort == 'stock_level_asc' ? 'stock_level_desc' : ($sort == 'latest' ? 'stock_level_asc' : "latest")]) }}" class="btn btn-sm m-0 btn-link p-0">
                            @if($sort == 'stock_level_asc')
                                <svg xmlns="http://www.w3.org/2000/svg" style="stroke: black !important; width: 18px !important; height: 18px !important" class="icon icon-tabler icon-tabler-arrow-narrow-up" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14" />
                                <path d="M16 9l-4 -4" />
                                <path d="M8 9l4 -4" />
                              </svg>
                            @elseif($sort == 'stock_level_desc')
                                <svg xmlns="http://www.w3.org/2000/svg" style="stroke: black !important; width: 18px !important; height: 18px !important" class="icon icon-tabler icon-tabler-arrow-narrow-down" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14" />
                                <path d="M16 15l-4 4" />
                                <path d="M8 15l4 4" />
                              </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" style="stroke: black !important; width: 18px !important; height: 18px !important" class="icon icon-tabler icon-tabler-arrows-sort" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 9l4 -4l4 4m-4 -4v14" />
                                <path d="M21 15l-4 4l-4 -4m4 4v-14" />
                              </svg>
                            @endif
                        </a>
                    </div>
                    </th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Discount</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Discount Expired at</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Code</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Site</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Controls</th>
                </tr>
              </thead>
              <tbody>
                @if($products->count() > 0)
                @foreach ($products as $index => $product)
                    <tr>
                    <td>
                        <div class="w-100 d-flex justify-content-center align-item-center">
                            <input type="checkbox" name="product{{$index}}" style="width: 15px;height: 15px" id="product{{$index}}" value="{{$product->id}}" v-model="selected_products">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex px-2 py-1"style="min-width: 300px;">
                        <div>
                            <img src="{{$product->image}}" class="avatar avatar-lg me-3" alt="user1">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm" style="max-width: 420px;  overflow: hidden; text-overflow:ellipsis;white-space: wrap;">{{$product->name}}</h6>
                        </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->price }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->value_price }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                        @switch($product->stock)
                            @case(1)
                                <span class="badge badge-sm bg-gradient-success">In Stock</span>
                                @break
                            @case(2)
                                <span class="badge badge-sm bg-gradient-success">In Stock</span>
                                @break
                            @default
                                <span class="badge badge-sm bg-gradient-danger">Out Of Stock</span>

                        @endswitch
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->stock_level }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->discount_value }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->discount_exp ? Carbon\Carbon::parse($product->discount_exp)->format('d-m-Y') : '' }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $product->code }}</p>
                    </td>
                    <td>
                        Costco Uk
                    </td>
                    <td>
                        <div class="d-flex" style="gap: 12px">
                            <button class="btn btn-danger remove-product" data-product-id="{{ $product->id }}">
                                Remove
                            </button>
                            <a href="{{$product->url}}" target="blanck" class="btn btn-success ml-2">Link</a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center p-4">
                            There is no products yet !
                        </td>
                    </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
        <div class="d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section("scripts")
<script>
    const { createApp, ref } = Vue;

    createApp({
      data() {
        return {
            urls: [""],
            selected_products: []
        }
      },
      methods: {
        removeUrl(index) {
            this.urls.splice(index, 1);
        },
        deleteAll() {
        // Make an AJAX request to remove all selected products
        if (confirm("Are you sure you want to remove all selected products?")) {
            $("#delete-all").trigger("submit")
        }
        }
        }
    }).mount('#dash_wrapper')
</script>

@endsection
