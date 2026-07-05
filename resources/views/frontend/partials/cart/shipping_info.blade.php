@php
    $address = $address ?? null;
@endphp
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Nav Tabs Start -->
 <div class="container-fluid px-0">
    <div class="premium-tabs-wrapper">

        <ul class="nav nav-tabs justify-content-start border-0"
            id="shippingTab"
            role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active"
                   id="home-tab"
                   data-toggle="tab"
                   href="#shipping-address"
                   role="tab"
                   aria-controls="ShippingAddress"
                   aria-selected="true">
                    {{ translate('Shipping Address') }}
                </a>
            </li>

            @if (get_setting('billing_address_required'))
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   id="profile-tab"
                   data-toggle="tab"
                   href="#billing-address"
                   role="tab"
                   aria-controls="BillingAddress"
                   aria-selected="false">
                    {{ translate('Billing Address') }}
                </a>
            </li>
            @endif

        </ul>

    </div>
</div>
    <!-- Nav Tabs End -->

@if (Auth::check())

    <div class="tab-content" id="shippingTabContent">
        <!--Shipping Address-->
        <div class="tab-pane fade show active" id="shipping-address" role="tabpanel"
            aria-labelledby="shipping-address-tab">
            <div class="d-flex justify-content-end choose-address">
                <button type="button" class="px-0 py-1 border-0 bg-white fs-12 fw-bold text-blue" data-toggle="modal"
                    data-target="#choose-address-modal">{{ translate('Choose Another
                    Address') }}</button>
            </div>
            <!-- Single Start -->
            <div class="mb-2 mt-2 mt-md-3">
                @php
                $address = Auth::user()->addresses()->where('id', $address_id)->first();
                if($address){
                $city = optional($address->city);
                $area_id = $address->area_id;

                $has_area_id = !is_null($area_id);
                $city_status = $city->status;
                $active_area_exists = $city->areas()->where('status', 1)->exists(); 
                $area_status = $has_area_id ? optional($address->area)->status : 1;
                $is_disabled =
                    $city_status === 0 ||
                    ($has_area_id && $area_status === 0) ||
                    ($active_area_exists && !$has_area_id) ||
                    ($address->state_id == null && get_setting('has_state') == 1);
                }
                @endphp

                @if($address)
                <div class="card border-0 rounded-4 {{ $is_disabled ? 'border border-danger' : '' }} mb-3" id="default-address-box" style="box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
                    <div class="row align-items-center m-0">
                        <div class="col-8 p-0">
                            <label class="aiz-megabox d-block bg-white mb-0 h-100">
                                <input type="radio" name="single_address_id" value="{{ $address->id }}" {{ $address->id == $address_id && !$is_disabled ? 'checked' : '' }} {{ $is_disabled ? 'disabled' : '' }}>
                                <span class="d-flex p-3 p-md-4 aiz-megabox-elem border-0 h-100 align-items-center">
                                    <span class="aiz-rounded-check flex-shrink-0" style="border-color: #502288;"></span>
                                    <span class="pl-3 text-left" id="choose-default" style="line-height: 1.4;">
                                        <span class="d-block text-dark fw-600 fs-14 mb-1">{{ $address->address }}</span>
                                        <span class="d-block text-muted fs-13 mb-1">{{ $address->area ? $address->area->name . ',' : '' }} {{ $address->postal_code }}-{{ $address->city->name }}, {{ $address->state && $address->state->status == 1 ? $address->state->name . ',' : '' }} {{ optional($address->country)->name }}</span>
                                        <span class="d-block text-primary fw-600 fs-13"><i class="las la-phone mr-1"></i>{{ $address->phone }}</span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <div class="col-4 p-3 text-right" style="background-color: #fafbfc; height: 100%; border-left: 1px dashed #eee;">
                            <button type="button" id="default-address-change-btn" class="btn btn-sm text-white rounded-pill px-3 py-1 shadow-sm mt-3" style="background: linear-gradient(135deg, #502288, #7a3bc7); font-weight: 600;" onclick="edit_address('{{ $address->id }}')">
                                {{ translate('Edit') }}
                            </button>
                        </div>

                        @if($is_disabled)
                        <div class="col-12 p-2" id="hide-no-longer-div" style="background-color: #fff1f0;">
                            <div class="text-center text-danger fs-12 fw-600">
                                <i class="las la-exclamation-circle mr-1"></i>{{ translate('We no longer deliver in this area.') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <input type="hidden" name="checkout_type" value="logged">

                <div class="d-flex flex-wrap align-items-center justify-content-between mt-4">
                    @if($address)
                    <div class="form-group form-check px-0 py-1 m-0">
                        <label class="aiz-checkbox m-0 d-flex align-items-center">
                            <input type="radio" data-type="shipping" name="billing_address_id" value="" required>
                            <span class="aiz-square-check mr-2"></span>
                            <span class="fs-14 fw-600 text-dark">{{ translate('Use this as billing address') }}</span>
                        </label>
                    </div>
                    @endif
                    <!-- Add New Address -->
                    <div class="py-1">
                        <div class="c-pointer text-center py-2 px-4 shadow-sm has-transition d-flex align-items-center justify-content-center rounded-pill"
                            style="background-color: #eff3fa;"
                            onclick="add_new_address()">
                            <i class="las la-plus fs-18 fw-bold text-primary mr-1"></i>
                            <span class="fs-13 text-primary fw-700">{{ translate('Add New Address') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Single End -->
        </div>
        <!--Shipping End-->

        @if (get_setting('billing_address_required'))
        <!--Billing Address Start-->
        <div class="tab-pane fade" id="billing-address" role="tabpanel" aria-labelledby="billing-address-tab">
             <div class="d-flex justify-content-end choose-address">
                <button type="button" class="px-0 py-1 border-0 bg-white fs-12 fw-bold text-blue" data-toggle="modal"
                    data-target="#choose-billing-address-modal">{{ translate('Choose Another Billing Address') }}</button>
            </div>
            <div class="mb-2 mt-2 mt-md-3">
                @php
                $address = Auth::user()->addresses()->where('set_billing', 1)->first();
                
                if($address){
                $city = optional($address->city);
                $area_id = $address->area_id;

                $has_area_id = !is_null($area_id);
                $city_status = $city->status;
                $active_area_exists = $city->areas()->where('status', 1)->exists(); 
                $area_status = $has_area_id ? optional($address->area)->status : 1;
                
                $is_disabled =
                    $city_status === 0 ||
                    ($has_area_id && $area_status === 0) ||
                    ($active_area_exists && !$has_area_id) ||
                    ($address->state_id == null && get_setting('has_state') == 1);
                }
                @endphp
                @if($address)
                <div class="border {{ $is_disabled ? ' border-danger' : '' }} mb-3" id="default-billing-address-box">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="aiz-megabox d-block bg-white mb-0">
                                <input type="radio" name="single_billing_address_id" data-type="billing" value="{{ $address->id }}" checked {{ $is_disabled ? 'disabled' : '' }} required >
                                <span class="d-flex p-3 aiz-megabox-elem border-0">
                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                    <span class="pl-3 text-left w-xl-300px" id="choose-default-billing">
                                        {{ $address->address }}, {{ $address->area ? $address->area->name . ',' : '' }} {{ $address->postal_code }}-{{ $address->city->name }},{{ $address->state && $address->state->status == 1 ? $address->state->name . ',' : '' }} {{ optional($address->country)->name }}
                                        <br>  {{ $address->phone }}
                                    </span>
                                </span>
                            </label>
                        </div>
                        <!-- Always show Change button -->
                        <div class="col-md-4 p-3 text-right">
                            <a id="billing-address-change-btn" class="btn btn-sm btn-secondary-base text-white mr-3 rounded-pill px-4"
                                onclick="edit_billing_address('{{ $address->id }}')">
                                {{ translate('Change') }}
                            </a>
                        </div>

                        @if($is_disabled)
                        <div class="col-md-12" id="hide-no-valid-div">
                            <div class="text-center text-danger">
                                <span>{{ translate('Address Not Valid, Choose Another') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="d-flex flex-wrap align-items-center justify-content-end">
                    <!-- Add New Address -->
                    <div class="py-1">
                        <div class="border c-pointer text-center py-2 px-3 bg-soft-blue has-transition d-flex justify-content-center rounded-pill"
                            onclick="add_new_billing_address()">
                            <i class="las la-plus fs-20 fw-bold text-blue"></i>
                            <div class="alpha-7 fs-14 text-blue fw-700 ml-2">{{ translate('Add New Billing Address') }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!--Billing Address End-->
        @endif
    </div>


    <!--Modal Start -->
    <div class="modal fade" id="choose-address-modal" tabindex="-1" aria-labelledby="chooseAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chooseAddressModalLabel">Choose Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Single Start -->
                    <div>
                        @foreach (Auth::user()->addresses as $key => $address)
                        @php
                            $city = optional($address->city);
                            $area_id = $address->area_id;

                            $has_area_id = !is_null($area_id);
                            $city_status = $city->status;
                            $active_area_exists = $city->areas()->where('status', 1)->exists(); // new line
                            $area_status = $has_area_id ? optional($address->area)->status : 1;
                            $is_disabled =
                                $city_status === 0 ||
                                ($has_area_id && $area_status === 0) ||
                                ($active_area_exists && !$has_area_id) ||
                                ($address->state_id == null && get_setting('has_state') == 1);
                        @endphp
                        <div class="border {{ $is_disabled ? 'border-danger' : '' }} mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="aiz-megabox d-block bg-white mb-0">
                                        <input type="radio" name="address_id" value="{{ $address->id }}" {{ $address->id == $address_id && !$is_disabled ? 'checked' : '' }}
                                             {{ $is_disabled ? 'disabled' : '' }} required>
                                        <span class="d-flex p-3 aiz-megabox-elem border-0">
                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                            <span class="pl-3 text-left w-xl-300px address-text">
                                                {{ $address->address }}, {{ $address->area ? $address->area->name . ',' : '' }} {{ $address->postal_code }}-{{ $address->city->name }},{{ $address->state && $address->state->status == 1 ? $address->state->name . ',' : '' }} {{ optional($address->country)->name }}
                                              <br>  {{ $address->phone }}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <!-- Always show Change button -->
                                <div class="col-md-4 p-3 text-right">
                                    <a class="btn btn-sm btn-secondary-base text-white mr-4 rounded-pill px-4"
                                        onclick="edit_address('{{ $address->id }}')">
                                        {{ translate('Change') }}
                                    </a>
                                </div>
                                @if($is_disabled)
                                <div class="col-md-12">
                                    <div class="text-center text-danger">
                                        <span>{{ translate('We no longer deliver in this area.') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <input type="hidden" name="checkout_type" value="logged">

                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            
                            <!-- Add New Address Button -->
                            <div class="py-1">
                                <div class="border c-pointer text-center py-2 px-3 bg-soft-blue has-transition d-flex justify-content-center rounded-pill"
                                    onclick="add_new_address()">
                                    <i class="las la-plus fs-20 fw-bold text-blue"></i>
                                    <div class="alpha-7 fs-14 text-blue fw-700 ml-2">{{ translate('Add New Address') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single End -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-0" onclick="changeShippingAddress()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="choose-billing-address-modal" tabindex="-1" aria-labelledby="chooseAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chooseAddressModalLabel">{{ translate('Choose Billing Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Single Start -->
                    <div>
                        @foreach (Auth::user()->addresses as $key => $address)
                        @php
                            $city = optional($address->city);
                            $area_id = $address->area_id;

                            $has_area_id = !is_null($area_id);
                            $city_status = $city->status;
                            $active_area_exists = $city->areas()->where('status', 1)->exists(); // new line
                            $area_status = $has_area_id ? optional($address->area)->status : 1;
                            $is_disabled =
                                $city_status === 0 ||
                                ($has_area_id && $area_status === 0) ||
                                ($active_area_exists && !$has_area_id) ||
                                ($address->state_id == null && get_setting('has_state') == 1);
                        @endphp
                        <div class="border {{ $is_disabled ? 'border-danger' : '' }} mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="aiz-megabox d-block bg-white mb-0">
                                        <input type="radio" name="billing_address_id" data-type="billing" value="{{ $address->id }}" {{ $address->set_billing == 1 ? 'checked' : '' }}
                                                {{ $is_disabled ? 'disabled' : '' }} required>
                                        <span class="d-flex p-3 aiz-megabox-elem border-0">
                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                            <span class="pl-3 text-left w-xl-300px address-text">
                                                {{ $address->address }}, {{ $address->area ? $address->area->name . ',' : '' }} {{ $address->postal_code }}-{{ $address->city->name }},{{ $address->state && $address->state->status == 1 ? $address->state->name . ',' : '' }} {{ optional($address->country)->name }}
                                                <br>  {{ $address->phone }}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <!-- Always show Change button -->
                                <div class="col-md-4 p-3 text-right">
                                    <a class="btn btn-sm btn-secondary-base text-white mr-4 rounded-pill px-4"
                                        onclick="edit_billing_address('{{ $address->id }}')">
                                        {{ translate('Change') }}
                                    </a>
                                </div>
                                @if($is_disabled)
                                <div class="col-md-12">
                                    <div class="text-center text-danger">
                                        <span>{{ translate('Address is not Valid') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <input type="hidden" name="checkout_type" value="logged">

                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            
                            <!-- Add New Address Button -->
                            <div class="py-1">
                                <div class="border c-pointer text-center py-2 px-3 bg-soft-blue has-transition d-flex justify-content-center rounded-pill"
                                    onclick="add_new_address()">
                                    <i class="las la-plus fs-20 fw-bold text-blue"></i>
                                    <div class="alpha-7 fs-14 text-blue fw-700 ml-2">{{ translate('Add New Address') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single End -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-0 close" data-dismiss="modal" aria-label="Close">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    
    
    <!--Modal End -->
@else
    <!-- Guest Shipping a address -->
    @include('frontend.partials.cart.guest_shipping_info')
@endif
