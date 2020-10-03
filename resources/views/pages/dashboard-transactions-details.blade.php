@extends('layouts.dashboard')

@section('title')
    Store Dashboard Transactions Details
@endsection

@section('content')
            <div
            class="section-content section-dashboard-home"
            data-aos="fade-up"
          >
            <div class="container-fluid">
              <div class="dashboard-heading">
                <h2 class="dashboard-title">#{{ $transaction->code }}</h2>
                <p class="dashboard-subtitle">
                  Transaction Details
                </p>
              </div>
              <div class="dashboard-content" id="transactionDetails">
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-12 col-md-4">
                            <img
                              src="{{ Storage::url($transaction->product->galleries->first()->photos ?? '') }}"
                              alt=""
                              class="w-100 mb-3"
                            />
                          </div>
                          <div class="col-12 col-md-8">
                            <div class="row">
                              <div class="col-12 col-md-6">
                                <div class="product-title">Customer Name</div>
                                <div class="product-subtitle">{{ $transaction->transaction->user->name }}</div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Product Name</div>
                                <div class="product-subtitle">
                                  {{ $transaction->product->name }}
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">
                                  Date of Transaction
                                </div>
                                <div class="product-subtitle">
                                  {{ $transaction->created_at }}
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Payment Status</div>
                                <div class="product-subtitle">
                                    <span class="badge" :class="transactionClass">
                                        {{ $transaction->transaction->transaction_status }}
                                    </span>
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Total Amount</div>
                                <div class="product-subtitle">Rp.{{ number_format($transaction->transaction->total_price) }}</div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Mobile</div>
                                <div class="product-subtitle">
                                  {{ $transaction->transaction->user->phone_number }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12 mt-4">
                            <h5>
                              Shipping Informations
                            </h5>
                            <form @submit.prevent="onSubmit" action="{{ route('dashboard-transaction-update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                              <div class="col-12 col-md-12">
                                <div class="product-title">Complete address</div>
                                <div class="product-subtitle">
                                  {{ $transaction->transaction->user->address_one }}
                                </div>
                              </div>
                              {{-- <div class="col-12 col-md-6">
                                <div class="product-title">Address 2</div>
                                <div class="product-subtitle">
                                  Blok B2 No. 34
                                </div>
                              </div> --}}
                              <div class="col-12 col-md-6">
                                <div class="product-title">
                                  Sub District
                                </div>
                                <div class="product-subtitle">
                                  {{ $transaction->transaction->user->sub_district }}
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Village Districts</div>
                                <div class="product-subtitle">
                                  {{ $transaction->transaction->user->village_districts }}
                                </div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Postal Code</div>
                                <div class="product-subtitle">{{ $transaction->transaction->user->zip_code }}</div>
                              </div>
                              <div class="col-12 col-md-6">
                                <div class="product-title">Phone Number</div>
                                <div class="product-subtitle">
                                  {{ $transaction->transaction->user->phone_number }}
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="row">
                                    @if($transaction->transaction->user->id == Auth::id())
                                        <div class="col-md-3">
                                            <div class="product-title">Shipping Status</div>
                                            <div class="product-subtitle" :class="shipmentClass">
                                                {{ $transaction->shipping_status }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-3">
                                            <div class="product-title">Shipping Status</div>
                                            <select
                                            name="shipping_status"
                                            id="status"
                                            class="form-control"
                                            v-model="status">                                    >
                                            <option value="PENDING">Pending</option>
                                            <option value="SHIPPING">Shipping</option>
                                            <option value="SUCCESS">Success</option>
                                            </select>
                                        </div>
                                        <template v-if="status == 'SHIPPING'">
                                            <div class="col-md-3">
                                            <div class="product-title">
                                                Input Resi
                                            </div>
                                            <input
                                                class="form-control"
                                                type="text"
                                                name="resi"
                                                id="openStoreTrue"
                                                v-model="resi"
                                            />
                                            </div>
                                            <div class="col-md-2">
                                            <button
                                                type="submit"
                                                class="btn btn-success btn-block mt-4"
                                            >
                                                Update Resi
                                            </button>
                                            </div>
                                        </template>
                                    @endif
                                </div>
                              </div>

                            </div>
                            <div class="row mt-4">
                              <div class="col-12 text-right">
                                <button
                                  type="submit"
                                  class="btn btn-success btn-lg mt-4"
                                >
                                  Save Now
                                </button>
                              </div>
                            </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

@push('addon-script')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/vendor/vue/vue.js"></script>
    <script>
      var transactionDetails = new Vue({
        el: "#transactionDetails",
        data: {
          status: "{{ $transaction->shipping_status }}",
          resi: "{{ $transaction->resi }}",
          transaction: "{{ $transaction->transaction->transaction_status }}",
          url: "{{ Request::url() }}"
        },
        computed: {
            shipmentClass() {
                if (this.status == "SUCCESS") {
                    return "text-succes"
                } else if(this.status == "SHIPPING"){
                    return "text-warning"
                } else {
                    return "text-danger"
                }
            },
            transactionClass() {
                if (this.transaction == "PAID") {
                    return "badge-success"
                } else if(this.transaction == "PENDING"){
                    return "badge-warning"
                } else {
                    return "badge-danger"
                }
            }
        },
        methods: {
            onSubmit() {
                axios.post(this.url, {shipping_status: this.status, resi: this.resi})
                .then(resp => {
                    if (resp.status == 200) {
                        Swal.fire('Success!', 'Shipment status updated', 'success')
                    }
                    this.status = resp.data.shipping_status
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something wrong, check the log', 'error')
                    console.log(error)
                })
            }
        }
      });
    </script>
@endpush
