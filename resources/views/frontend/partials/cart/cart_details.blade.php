<div class="cart-items-wrapper pb-5 mb-5">
    @php
        $cart_count = count($carts);
        $active_carts = $cart_count > 0 ? $carts->toQuery()->active()->get() : [];
        $total = 0;
    @endphp

    @if($cart_count > 0)
        <!-- Hidden Inputs for AJAX update -->
        <input type="checkbox" class="check-all d-none" checked>

        <ul class="list-group list-group-flush mb-4">
            @foreach ($carts as $key => $cartItem)
                @php
                    $product = get_single_product($cartItem['product_id']);
                    $product_stock = $product->stocks->where('variant', $cartItem->variation)->first();
                    $item_price = cart_product_price($cartItem, $product, false);
                    $total += $item_price * $cartItem->quantity;
                @endphp
                <li class="list-group-item px-0 border-0 mb-3 bg-transparent">
                    <!-- Checkbox (hidden but checked to keep logic intact) -->
                    <input type="checkbox" class="check-one d-none" name="id[]" value="{{$cartItem->product_id}}" checked>

                    <div class="d-flex align-items-center bg-white p-3 rounded-lg shadow-sm position-relative">
                        <!-- Remove Icon -->
                        <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $cartItem->id }})" class="position-absolute" style="top: 10px; right: 10px; color: #e74c3c; z-index: 2;">
                            <i class="las la-trash fs-20"></i>
                        </a>

                        <!-- Product Image -->
                        <div class="mr-3" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f8f9fa; flex-shrink: 0;">
                            <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->getTranslation('name') }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </div>

                        <!-- Product Details -->
                        <div class="flex-grow-1 d-flex flex-column justify-content-between h-100 py-1" style="min-width: 0;">
                            <div>
                                <h6 class="fs-14 fw-700 text-dark text-truncate mb-1 pr-4">{{ $product->getTranslation('name') }}</h6>
                                @if ($cartItem->variation != '')
                                    <div class="fs-12 text-muted mb-1">{{ $cartItem->variation }}</div>
                                @endif
                                <div class="fs-15 fw-800 text-primary">{{ single_price($item_price) }}</div>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="d-flex justify-content-end mt-2">
                                <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 aiz-plus-minus" style="max-width: 100px;">
                                    <button class="btn btn-icon btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center bg-white shadow-sm" type="button" data-type="minus" data-field="quantity[{{ $cartItem->id }}]" style="width: 24px; height: 24px;">
                                        <i class="las la-minus fs-12 text-dark"></i>
                                    </button>
                                    <input type="number" name="quantity[{{ $cartItem->id }}]" class="border-0 text-center flex-grow-1 bg-transparent fs-14 fw-700 text-dark input-number p-0" value="{{ $cartItem->quantity }}" data-weight="{{ $product->weight }}" min="{{ $product->min_qty }}" max="{{ $product_stock ? $product_stock->qty : 10 }}" onchange="updateQuantity({{ $cartItem->id }}, this)" style="width: 30px; outline: none; -webkit-appearance: none;">
                                    <button class="btn btn-icon btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center bg-white shadow-sm" type="button" data-type="plus" data-field="quantity[{{ $cartItem->id }}]" style="width: 24px; height: 24px;">
                                        <i class="las la-plus fs-12 text-dark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        
        <!-- Sticky Bottom Bar for Checkout -->
        <div class="position-fixed d-flex justify-content-between align-items-center p-3 bg-white" style="bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); z-index: 99; border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <div>
                <div class="fs-12 text-muted fw-600">Total Price</div>
                <div class="fs-20 fw-900 text-dark">{{ single_price($total) }}</div>
            </div>
            <div>
                @if(Auth::check())
                    <a href="{{ route('checkout') }}" class="btn text-white fw-700 px-4 py-2 d-flex align-items-center gap-2 rounded-pill shadow-sm" style="background-color: #502288; font-size: 15px;">
                        Checkout <i class="las la-arrow-right fs-18"></i>
                    </a>
                @else
                    <button class="btn text-white fw-700 px-4 py-2 d-flex align-items-center gap-2 rounded-pill shadow-sm" style="background-color: #502288; font-size: 15px;" onclick="showLoginModal()">
                        Checkout <i class="las la-arrow-right fs-18"></i>
                    </button>
                @endif
            </div>
        </div>

    @else
        <!-- Empty Cart -->
        <div class="d-flex flex-column align-items-center justify-content-center mt-5 pt-5">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                <i class="las la-shopping-cart" style="font-size: 50px; color: #ccc;"></i>
            </div>
            <h4 class="fw-700 text-dark mb-2">Your Cart is Empty</h4>
            <p class="text-muted text-center mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 fw-600">Start Shopping</a>
        </div>
    @endif
    
    <style>
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</div>
