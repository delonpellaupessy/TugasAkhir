@extends('layouts.app')

@section('title')
    Store Cart Page
@endsection

@section('content')
    <div class="page-content page-cart">
      <section
        class="store-breadcrumbs"
        data-aos="fade-down"
        data-aos-delay="100"
      >
        <div class="container">
          <div class="row">
            <div class="col-12">
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                    <a href="/index.html">Home</a>
                  </li>
                  <li class="breadcrumb-item">
                    Cart
                  </li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </section>

      <section class="store-cart">
          <div class="container">
          <div class="row" data-aos="fade-up" data-aos-delay="100">
              <div class="col-12 table-responsive">
              <table
                  class="table table-borderless table-cart"
                  aria-describedby="Cart"
              >
                  <thead>
                  <tr>
                      <th scope="col">Image</th>
                      <th scope="col">Name &amp; Seller</th>
                      <th scope="col">Price</th>
                      <th scope="col">Menu</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php $totalPrice = 15000 @endphp
                  @foreach ($carts as $cart)
                      <tr>
                      <td style="width: 25%;">
                          @if ($cart->product->galleries)
                          <img
                          src="{{ Storage::url($cart->product->galleries->first()->photos) }}"
                          alt=""
                          class="cart-image"
                          />
                          @endif
                      </td>
                      <td style="width: 35%;">
                          <div class="product-title">{{ $cart->product->name }}</div>
                          <div class="product-subtitle">{{ $cart->product->user->store_name }}</div>
                      </td>
                      <td style="width: 35%;">
                          <div class="product-title">{{ number_format($cart->product->price) }}</div>
                          <div class="product-subtitle">Rp.</div>
                      </td>
                      <td style="width: 20%;">
                          <form action="{{ route('cart-delete', $cart->id) }}" method="POST">
                          @method('DELETE')
                          @csrf
                          <button type="submit" class="btn btn-remove-cart">
                              Remove
                          </button>
                          </form>
                      </td>
                      </tr>
                      @php $totalPrice += $cart->product->price @endphp
                  @endforeach
                  </tbody>
              </table>
              </div>
          </div>
          <div class="row" data-aos="fade-up" data-aos-delay="150">
              <div class="col-12">
              <hr />
              </div>
              <div class="col-12">
              <h2 class="mb-4">Shipping Details</h2>
              </div>
          </div>
          <form action="{{ route('checkout') }}" id="locations" enctype="multipart/form-data" method="POST">
              @csrf
              <input type="hidden" name="total_price" value="{{ $totalPrice }}">
              <div class="row mb-2" data-aos="fade-up" data-aos-delay="200">
              <div class="col-md-12">
                  <div class="form-group">
                  <label for="address_one">Complete address</label>
                  <input
                      type="text"
                      class="form-control"
                      id="address_one"
                      name="address_one"
                      value="{{ $cart->user->address_one ?? ''}}"
                  />
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                  <label for="sub_district">Sub District</label>
                  <input
                      type="text"
                      class="form-control"
                      id="sub_district"
                      name="sub_district"
                      value="{{ $cart->user->sub_district ?? ''}}"
                  />
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                  <label for="village_districts">Village Districts</label>
                  <input
                      type="text"
                      class="form-control"
                      id="village_districts"
                      name="village_districts"
                      value="{{ $cart->user->village_districts ?? ''}}"
                  />
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                  <label for="rtrw">RT/RW</label>
                  <input
                      type="text"
                      class="form-control"
                      id="rtrw"
                      name="rtrw"
                      value="{{ $cart->user->rtrw ?? '' }}"
                  />
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                  <label for="zip_code">Postal Code</label>
                  <input
                      type="text"
                      class="form-control"
                      id="zip_code"
                      name="zip_code"
                      value="{{ $cart->user->zip_code ?? '' }}"
                  />
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                  <label for="phone_number">Mobile</label>
                  <input
                      type="text"
                      class="form-control"
                      id="phone_number"
                      name="phone_number"
                      value="{{ $cart->user->phone_number ?? '' }}"
                  />
                  </div>
              </div>
              </div>
              <div class="row" data-aos="fade-up" data-aos-delay="150">
              <div class="col-12">
                  <hr />
              </div>
              <div class="col-12">
                  <h2>Payment Informations</h2>
              </div>
              </div>
              <div class="row" data-aos="fade-up" data-aos-delay="200">
              <div class="col-4 col-md-3">
                  <div class="product-title">Rp.15.000</div>
                  <div class="product-subtitle">Shipping Costs</div>
              </div>
              <div class="col-4 col-md-3">
                  <div class="product-title text-success">Rp.{{ number_format($totalPrice ?? 0) }}</div>
                  <div class="product-subtitle">Total</div>
              </div>
              <div class="col-8 col-md-3">
                  <button
                  type="submit"
                  class="btn btn-success mt-4 px-4 btn-block"
                  >
                  Checkout Now
                  </button>
              </div>
              </div>
          </form>
          </div>
      </section>
    </div>
@endsection

@push('addon-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
      $(document).ready(function () {
        $("#locations").submit(function (e) {
          e.preventDefault();
          // alert confirmation
          Swal.fire({
            title: 'Checkout now?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, checkout!',
          }).then((result) => {
            // Yes, checkout
            if(result.isConfirmed) {
              // show loading
              Swal.fire({
                  title: 'Please Wait !',
                  text: 'Processing your transaction...',
                  allowOutsideClick: false,
                  willOpen: () => {
                      Swal.showLoading()
                  },
              });
              // process ajax transaction
              setTimeout(() => {
                showAlert('pending')
              }, 5000);
            }
          })

          // $.ajax({
          //   type: "POST",
          //   url: "{{ route('checkout-ajax') }}",
          //   data: $(this).serialize(),
          //   dataType: "Json",
          //   success: function (response) {
          //     console.log(response)

          //   }
          // });
        });

        function process(token) {
          snap.pay(response, {
            onSuccess: function(result){console.log('success');console.log(result);},
            onPending: function(result){console.log('pending');console.log(result);},
            onError: function(result){console.log('error');console.log(result);},
            onClose: function(){console.log('customer closed the popup without finishing the payment');}
          })
        }

        function showAlert(status) {
          switch (status) {
            case "success":
                Swal.fire({
                  title: 'Success!',
                  text: "Your payment is successfully paid",
                  icon: 'success',
                  allowOutsideClick: 'false',
                  confirmButtonText: 'Back to Dashboard',
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = "/dashboard";
                  }
                })
              break;
            case "pending":
                Swal.fire({
                  title: 'Success!',
                  text: "Transaction created. Waiting for your payment.",
                  icon: 'success',
                  allowOutsideClick: 'false',
                  confirmButtonText: 'Back to Dashboard',
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = "/dashboard";
                  }
                })
              break;
            case "error":
                Swal.fire({
                  title: 'Error!',
                  text: "Your payment has failed",
                  icon: 'error',
                  allowOutsideClick: 'false',
                  confirmButtonText: 'Back to Dashboard',
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = "/dashboard";
                  }
                })
              break;
            default:
              break;
          }
        }
      });
    </script>
@endpush
